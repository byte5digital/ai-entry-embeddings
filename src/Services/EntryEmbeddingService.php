<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services;

use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Statamic\Http\Requests\FilteredRequest;
use Statamic\Query\Scopes\Filters\Concerns\QueriesFilters;

final class EntryEmbeddingService implements EntryEmbeddingServiceInterface
{
    use QueriesFilters;
    /**
     * @param  ContentChunk[]  $chunks
     */
    public function replaceForEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        array $chunks,
    ): void {
        DB::transaction(function () use ($entryId, $collectionHandle, $siteHandle, $chunks): void {
            EntryEmbedding::query()->where('entry_id', $entryId)
                ->where('collection_handle', $collectionHandle)
                ->delete();

            $now = now();

            $rows = array_map(fn (ContentChunk $chunk): array => [
                'entry_id' => $entryId,
                'collection_handle' => $collectionHandle,
                'site_handle' => $siteHandle,
                'field_handle' => $chunk->fieldHandle,
                'path' => $chunk->path,
                'content' => $chunk->text,
                'metadata' => json_encode($chunk->metadata),
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunks);

            EntryEmbedding::query()->insert($rows);
        });
    }

    /**
     * @return Collection<string, EntryEmbedding>
     */
    public function getCollectionStats(): Collection
    {
        return EntryEmbedding::query()
            ->select('collection_handle')
            ->selectRaw('COUNT(DISTINCT entry_id) as entries_count')
            ->selectRaw('COUNT(*) as total_chunks')
            ->selectRaw('COUNT(embedding) as embedded_chunks')
            ->groupBy('collection_handle')
            ->get()
            ->keyBy('collection_handle');
    }

    /** @inheritDoc */
    public function getFilteredEmbeddings(FilteredRequest $request, string $collection): array
    {
        $query = EntryEmbedding::query()
            ->select(['entry_id', 'collection_handle', 'site_handle'])
            ->selectRaw('COUNT(*) as total_chunks')
            ->selectRaw('COUNT(embedding) as embedded_chunks')
            ->selectRaw('MAX(updated_at) as updated_at')
            ->groupBy('entry_id', 'collection_handle', 'site_handle')
            ->where('collection_handle', $collection);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('entry_id', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $activeFilterBadges = $this->queryFilters($query, $request->filters, []);

        $sortField = $request->input('sort', 'updated_at');
        $sortDirection = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortDirection);

        return [
            'paginator' => $query->paginate($request->input('perPage', 15)),
            'activeFilterBadges' => $activeFilterBadges,
        ];
    }
}
