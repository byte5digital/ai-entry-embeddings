<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Controllers;

use Byte5\AiEntryEmbeddings\Exceptions\EmbeddingResourceNotFoundException;
use Byte5\AiEntryEmbeddings\Http\Resources\EmbeddingCollectionsCollection;
use Byte5\AiEntryEmbeddings\Http\Resources\EntryEmbeddingChunksCollection;
use Byte5\AiEntryEmbeddings\Http\Resources\EntryEmbeddingWithStatusesCollection;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\Entries\Collection;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Scope;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Http\Requests\FilteredRequest;

final class AiEntryEmbeddingsController extends CpController
{
    public function __construct(
        private readonly EmbeddingCollectionRepositoryInterface $repository,
    ) {}

    public function landingPage(): Response
    {
        return Inertia::render('ai-entry-embeddings::LandingPage', [
            'definedEmbeddingCollections' => $this->repository->handles(),
            'listingUrl' => cp_route('ai-entry-embeddings.collections.list'),
        ]);
    }

    public function listCollections(
        EntryEmbeddingServiceInterface $service,
    ): EmbeddingCollectionsCollection {
        $stats = $service->getCollectionStats();

        $collections = array_map(function (string $handle) use ($stats) {
            $stat = $stats->get($handle);

            return [
                'handle' => $handle,
                'title' => ucfirst($handle),
                'url' => cp_route('ai-entry-embeddings.generatedEmbeddings', ['embeddingCollection' => $handle]),
                'entries_count' => $stat->entries_count ?? 0,
                'total_chunks' => $stat->total_chunks ?? 0,
                'embedded_chunks' => $stat->embedded_chunks ?? 0,
                'pending_chunks' => $stat->pending_chunks ?? 0,
            ];
        }, $this->repository->handles());

        return new EmbeddingCollectionsCollection($collections);
    }

    public function generatedEmbeddings(string $embeddingCollection): Response
    {
        $collection = $this->resolveCollection($embeddingCollection);

        return Inertia::render('ai-entry-embeddings::GeneratedEmbeddings', [
            'listingUrl' => cp_route('ai-entry-embeddings.embeddings.list', ['embeddingCollection' => $collection->handle()]),
            'filters' => Scope::filters('embeddings'),
            'embeddingCollection' => $collection->handle(),
        ]);
    }

    public function listEmbeddings(
        FilteredRequest $request,
        EntryEmbeddingServiceInterface $service,
        string $embeddingCollection,
    ): EntryEmbeddingWithStatusesCollection {
        $collection = $this->resolveCollection($embeddingCollection);
        $result = $service->getFilteredEmbeddings($request, $collection->handle());

        return (new EntryEmbeddingWithStatusesCollection($result['paginator']))
            ->additional([
                'activeFilterBadges' => $result['activeFilterBadges'],
            ]);
    }

    public function entryEmbeddingChunks(string $embeddingCollection, string $embeddingEntryId): Response
    {
        $collection = $this->resolveCollection($embeddingCollection);
        $entry = $this->resolveEntry($embeddingEntryId, $collection);

        return Inertia::render('ai-entry-embeddings::EntryEmbeddingChunks', [
            'listingUrl' => cp_route('ai-entry-embeddings.entryEmbeddingChunks.list', [
                'embeddingCollection' => $collection->handle(),
                'embeddingEntryId' => $entry->id(),
            ]),
            'embeddingCollection' => $collection->handle(),
            'embeddingEntryId' => $entry->id(),
            'entryTitle' => $entry->get('title') ?? $entry->id(),
        ]);
    }

    public function listEntryEmbeddingChunks(
        FilteredRequest $request,
        EntryEmbeddingServiceInterface $service,
        string $embeddingCollection,
        string $embeddingEntryId,
    ): EntryEmbeddingChunksCollection {
        $collection = $this->resolveCollection($embeddingCollection);
        $entry = $this->resolveEntry($embeddingEntryId, $collection);
        $result = $service->getFilteredEntryChunks($request, $collection->handle(), $entry->id());

        return (new EntryEmbeddingChunksCollection($result['paginator']))
            ->additional([
                'activeFilterBadges' => $result['activeFilterBadges'],
            ]);
    }

    private function resolveCollection(string $handle): Collection
    {
        $collection = CollectionFacade::findByHandle($handle);

        if (! $collection || ! $this->repository->exists($handle)) {
            throw EmbeddingResourceNotFoundException::collection();
        }

        return $collection;
    }

    private function resolveEntry(string $id, Collection $collection): Entry
    {
        $entry = EntryFacade::find($id);

        if (! $entry instanceof Entry || $entry->collection()->handle() !== $collection->handle()) {
            throw EmbeddingResourceNotFoundException::entry();
        }

        return $entry;
    }
}
