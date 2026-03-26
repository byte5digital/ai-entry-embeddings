<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services\Contracts;

use Byte5\AiEntryEmbeddings\Enums\EmbeddingStatus;
use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Statamic\Http\Requests\FilteredRequest;

interface EntryEmbeddingServiceInterface
{
    /**
     * Create or update the parent entry embedding and set its status.
     */
    public function upsertEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        EmbeddingStatus $status,
    ): EntryEmbedding;

    /**
     * Replace all chunks for an entry with new ones.
     *
     * @param  ContentChunk[]  $chunks
     */
    public function replaceChunks(EntryEmbedding $entryEmbedding, array $chunks): void;

    /**
     * Increment the embedded_chunks counter and update status if complete.
     */
    public function markChunksEmbedded(EntryEmbedding $entryEmbedding, int $count): void;

    /**
     * @return Collection<string, EntryEmbedding>
     */
    public function getCollectionStats(): Collection;

    /**
     * @return array{paginator: LengthAwarePaginator<int, EntryEmbedding>, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEmbeddings(FilteredRequest $request, string $collection): array;

    /**
     * Delete the entry embedding and all its chunks.
     */
    public function deleteForEntry(string $entryId): void;

    /**
     * @return array{paginator: LengthAwarePaginator<int, \Byte5\AiEntryEmbeddings\Models\EntryEmbeddingChunk>, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEntryChunks(FilteredRequest $request, string $collection, string $entryId): array;

    /**
     * Find the parent entry embedding row.
     */
    public function findForEntry(string $entryId, string $collectionHandle): ?EntryEmbedding;
}
