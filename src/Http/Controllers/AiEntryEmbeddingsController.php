<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Controllers;

use Byte5\AiEntryEmbeddings\Http\Resources\EmbeddingCollectionsCollection;
use Byte5\AiEntryEmbeddings\Http\Resources\EntryEmbeddingChunksCollection;
use Byte5\AiEntryEmbeddings\Http\Resources\EntryEmbeddingWithStatusesCollection;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\Facades\Entry;
use Statamic\Facades\Scope;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Http\Requests\FilteredRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class AiEntryEmbeddingsController extends CpController
{

    public function landingPage(  EmbeddingCollectionRepositoryInterface $repository,): Response
    {
        return Inertia::render('ai-entry-embeddings::LandingPage', [
            'definedEmbeddingCollections' => $repository->handles(),
            'listingUrl' => cp_route('ai-entry-embeddings.collections.list'),
        ]);
    }

    public function listCollections(
        EntryEmbeddingServiceInterface $service,
        EmbeddingCollectionRepositoryInterface $repository,
    ): EmbeddingCollectionsCollection {
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
        }, $repository->handles());

        return new EmbeddingCollectionsCollection(array_values($collections));
    }

    public function generatedEmbeddings(
        string $embeddingCollection,
        EmbeddingCollectionRepositoryInterface $repository,
    ): Response|SymfonyResponse {
        if (! $repository->exists($embeddingCollection)) {
            return Inertia::render('ai-entry-embeddings::NotFound')
                ->toResponse(request())
                ->setStatusCode(404);
        }
        return Inertia::render('ai-entry-embeddings::GeneratedEmbeddings', [
            'listingUrl' => cp_route('ai-entry-embeddings.embeddings.list', ['embeddingCollection' => $embeddingCollection]),
            'filters' => Scope::filters('embeddings'),
            'embeddingCollection' => $embeddingCollection,
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

    public function entryEmbeddingChunks(
        string $embeddingCollection,
        string $embeddingEntryId,
        EmbeddingCollectionRepositoryInterface $repository,
    ): Response|SymfonyResponse {
        if (! $repository->exists($embeddingCollection)) {
            return Inertia::render('ai-entry-embeddings::NotFound')
                ->toResponse(request())
                ->setStatusCode(404);
        }
        $entry = Entry::find($embeddingEntryId);
        $entryTitle = $entry?->get('title') ?? $embeddingEntryId;

        return Inertia::render('ai-entry-embeddings::EntryEmbeddingChunks', [
            'listingUrl' => cp_route('ai-entry-embeddings.entryEmbeddingChunks.list', [
                'embeddingCollection' => $embeddingCollection,
                'embeddingEntryId' => $embeddingEntryId,
            ]),
            'embeddingCollection' => $embeddingCollection,
            'embeddingEntryId' => $embeddingEntryId,
            'entryTitle' => $entryTitle,
        ]);
    }

    public function listEntryEmbeddingChunks(
        FilteredRequest $request,
        EntryEmbeddingServiceInterface $service,
        string $embeddingCollection,
        string $embeddingEntryId,
    ): EntryEmbeddingChunksCollection {
        $result = $service->getFilteredEntryChunks($request, $embeddingCollection, $embeddingEntryId);

        return (new EntryEmbeddingChunksCollection($result['paginator']))
            ->additional([
                'activeFilterBadges' => $result['activeFilterBadges'],
            ]);
    }
}
