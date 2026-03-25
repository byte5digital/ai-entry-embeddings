<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services\Contracts;

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
     * @return Collection<string, \Byte5\AiEntryEmbeddings\Models\EntryEmbedding>
     */
    public function getCollectionStats(): Collection;

    /**
     * @return array{paginator: LengthAwarePaginator, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEmbeddings(FilteredRequest $request, string $collection): array;

    /**
     * Delete all embedding chunks for a given entry.
     */
    public function deleteForEntry(string $entryId): void;
}
