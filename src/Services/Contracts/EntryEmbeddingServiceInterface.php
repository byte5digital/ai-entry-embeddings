<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services\Contracts;

use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Statamic\Http\Requests\FilteredRequest;

interface EntryEmbeddingServiceInterface
{
    /**
     * Replace all chunks for an entry with new ones.
     *
     * @param  ContentChunk[]  $chunks
     */
    public function replaceForEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        array $chunks,
    ): void;

    /**
     * @return Collection<string, EntryEmbedding>
     */
    public function getCollectionStats(): Collection;

    /**
     * @return array{paginator: LengthAwarePaginator<int, EntryEmbedding>, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEmbeddings(FilteredRequest $request, string $collection): array;

    /**
     * Delete all embedding chunks for a given entry.
     */
    public function deleteForEntry(string $entryId): void;

    /**
     * @return array{paginator: LengthAwarePaginator<int, EntryEmbedding>, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEntryChunks(FilteredRequest $request, string $collection, string $entryId): array;

    /**
     * Get embedding stats for a single entry.
     *
     * @return array{total_chunks: int, embedded_chunks: int, pending_chunks: int, status: string, updated_at: string|null}|null
     */
    public function getEntryStats(string $entryId, string $collectionHandle): ?array;
}
