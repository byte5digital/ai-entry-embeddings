<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services;

use Byte5\AiEntryEmbeddings\Enums\EmbeddingStatus;
use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Models\EntryEmbeddingChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Statamic\Http\Requests\FilteredRequest;
use Statamic\Query\Scopes\Filters\Concerns\QueriesFilters;

final class EntryEmbeddingService implements EntryEmbeddingServiceInterface
{
    use QueriesFilters;

    public function upsertEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        EmbeddingStatus $status,
    ): EntryEmbedding {
        $entryEmbedding = EntryEmbedding::query()->updateOrCreate(
            ['entry_id' => $entryId, 'collection_handle' => $collectionHandle],
            ['site_handle' => $siteHandle, 'status' => $status],
        );

        if ($status === EmbeddingStatus::Extracting) {
            $entryEmbedding->chunks()->delete();
            $entryEmbedding->update(['total_chunks' => 0, 'embedded_chunks' => 0]);
        }

        return $entryEmbedding;
    }

    /**
     * @param  ContentChunk[]  $chunks
     */
    public function replaceChunks(EntryEmbedding $entryEmbedding, array $chunks): void
    {
        DB::transaction(function () use ($entryEmbedding, $chunks): void {
            $entryEmbedding->chunks()->delete();

            $now = now();

            $rows = array_map(fn (ContentChunk $chunk): array => [
                'entry_embedding_id' => $entryEmbedding->id,
                'field_handle' => $chunk->fieldHandle,
                'path' => $chunk->path,
                'content' => $chunk->text,
                'metadata' => json_encode($chunk->metadata),
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunks);

            EntryEmbeddingChunk::query()->insert($rows);

            $entryEmbedding->update([
                'total_chunks' => count($chunks),
                'embedded_chunks' => 0,
            ]);
        });
    }

    public function markChunksEmbedded(EntryEmbedding $entryEmbedding, int $count): void
    {
        $entryEmbedding->update([
            'embedded_chunks' => $count,
            'status' => $count >= $entryEmbedding->total_chunks
                ? EmbeddingStatus::Generated
                : EmbeddingStatus::Generating,
        ]);
    }

    /**
     * @return Collection<string, EntryEmbedding>
     */
    public function getCollectionStats(): Collection
    {
        return EntryEmbedding::query()
            ->select('collection_handle')
            ->selectRaw('COUNT(*) as entries_count')
            ->selectRaw('SUM(total_chunks) as total_chunks')
            ->selectRaw('SUM(embedded_chunks) as embedded_chunks')
            ->groupBy('collection_handle')
            ->get()
            ->keyBy('collection_handle');
    }

    /** {@inheritDoc} */
    public function deleteForEntry(string $entryId): void
    {
        EntryEmbedding::query()->where('entry_id', $entryId)->delete();
    }

    /**
     * @return array{paginator: LengthAwarePaginator<int, EntryEmbedding>, activeFilterBadges: array<int, mixed>}
     */
    public function getFilteredEmbeddings(FilteredRequest $request, string $collection): array
    {
        $query = EntryEmbedding::query()
            ->where('collection_handle', $collection);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('entry_id', 'like', "%{$search}%")
                    ->orWhereHas('chunks', fn ($cq) => $cq->where('content', 'like', "%{$search}%"));
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

    /** {@inheritDoc} */
    public function getFilteredEntryChunks(FilteredRequest $request, string $collection, string $entryId): array
    {
        $entryEmbedding = $this->findForEntry($entryId, $collection);

        if ($entryEmbedding === null) {
            return [
                'paginator' => EntryEmbeddingChunk::query()->whereRaw('1 = 0')->paginate(),
                'activeFilterBadges' => [],
            ];
        }

        $query = $entryEmbedding->chunks();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                    ->orWhere('field_handle', 'like', "%{$search}%")
                    ->orWhere('path', 'like', "%{$search}%");
            });
        }

        $sortField = $request->input('sort', 'updated_at');
        $sortDirection = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortDirection);

        return [
            'paginator' => $query->paginate($request->input('perPage', 15)),
            'activeFilterBadges' => [],
        ];
    }

    public function findForEntry(string $entryId, string $collectionHandle): ?EntryEmbedding
    {
        return EntryEmbedding::query()
            ->where('entry_id', $entryId)
            ->where('collection_handle', $collectionHandle)
            ->first();
    }
}
