<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Resources;

use Byte5\AiEntryEmbeddings\Models\EntryEmbeddingChunk;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Statamic\CP\Column;

/**
 * @mixin EntryEmbeddingChunk
 */
final class EntryEmbeddingChunkResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'field_handle' => $this->field_handle,
            'path' => $this->path,
            'content' => $this->content,
            'embedding_status' => $this->embedding !== null ? 'generated' : 'pending',
            'metadata' => $this->metadata,
            'updated_at' => $this->updated_at,
        ];
    }

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make('field_handle')->label(__('ai-entry-embeddings::frontend.chunk.columns.field_handle'))->sortable(true),
            Column::make('path')->label(__('ai-entry-embeddings::frontend.chunk.columns.path'))->sortable(true),
            Column::make('content')->label(__('ai-entry-embeddings::frontend.chunk.columns.content'))->sortable(false),
            Column::make('embedding_status')->label(__('ai-entry-embeddings::frontend.chunk.columns.embedding_status'))->sortable(false),
            Column::make('metadata')->label(__('ai-entry-embeddings::frontend.chunk.columns.metadata'))->sortable(false),
            Column::make('updated_at')->label(__('ai-entry-embeddings::frontend.chunk.columns.updated_at'))->sortable(true),
        ];
    }
}
