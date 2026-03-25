<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Controllers;

use Byte5\AiEntryEmbeddings\Http\Resources\EntryEmbeddingWithStatusesCollection;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\CP\Column;
use Statamic\Facades\Scope;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Http\Requests\FilteredRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class AiEntryEmbeddingsController extends CpController
{

    public function landingPage(EntryEmbeddingServiceInterface $service): Response
    {
        $collectionHandles = array_keys(
            config('ai-entry-embeddings.extraction_pipeline.collections', [])
        );

        $stats = $service->getCollectionStats();

        $collections = array_map(function (string $handle) use ($stats) {
            $stat = $stats->get($handle);

            return [
                'handle' => $handle,
                'title' => ucfirst($handle),
                'url' => cp_route('ai-entry-embeddings.generatedEmbeddings', ['embeddingCollection' => $handle]),
                'entries_count' => $stat?->entries_count ?? 0,
                'total_chunks' => $stat?->total_chunks ?? 0,
                'embedded_chunks' => $stat?->embedded_chunks ?? 0,
                'pending_chunks' => $stat?->pending_chunks ?? 0,
            ];
        }, $collectionHandles);

        $columns = [
            Column::make('title')->label(__('Collection')),
            Column::make('entries_count')->label(__('Entries')),
            Column::make('embeddings')->label(__('Embeddings')),
        ];

        return Inertia::render('ai-entry-embeddings::LandingPage', [
            'collections' => array_values($collections),
            'columns' => $columns,
        ]);
    }

    public function generatedEmbeddings(string $embeddingCollection): Response|SymfonyResponse
    {
        if (! array_key_exists($embeddingCollection, config('ai-entry-embeddings.extraction_pipeline.collections', []))) {
            return Inertia::render('ai-entry-embeddings::NotFound')
                ->toResponse(request())
                ->setStatusCode(404);
        }
        return Inertia::render('ai-entry-embeddings::GeneratedEmbeddings', [
            'listingUrl' => cp_route('ai-entry-embeddings.embeddings.list', ['embeddingCollection' => $embeddingCollection]),
            'filters' => Scope::filters('embeddings'),
            'collection' => $embeddingCollection,
        ]);
    }

    public function listEmbeddings(
        FilteredRequest $request,
        EntryEmbeddingServiceInterface $service,
        string $embeddingCollection,
    ): EntryEmbeddingWithStatusesCollection {
        $result = $service->getFilteredEmbeddings($request, $embeddingCollection);

        return (new EntryEmbeddingWithStatusesCollection($result['paginator']))
            ->additional([
                'activeFilterBadges' => $result['activeFilterBadges'],
            ]);
    }
}
