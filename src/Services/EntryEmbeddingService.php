<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services;

use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Support\Facades\DB;

final class EntryEmbeddingService implements EntryEmbeddingServiceInterface
{
    /**
     * @param  ContentChunk[]  $chunks
     */
    public function replaceForEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        array $chunks,
    ): void {
        DB::transaction(function () use ($entryId, $collectionHandle, $siteHandle, $chunks) {
            EntryEmbedding::where('entry_id', $entryId)
                ->where('collection_handle', $collectionHandle)
                ->delete();

            $now = now();

            $rows = array_map(fn (ContentChunk $chunk) => [
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

            EntryEmbedding::insert($rows);
        });
    }
}
