<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Resources;

use Byte5\AiEntryEmbeddings\Enums\EmbeddingStatus;
use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Statamic\CP\Column;
use Statamic\Facades\Entry;

/**
 * @mixin EntryEmbedding
 */
final class EntryEmbeddingWithStatusResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $entry = Entry::find($this->entry_id);

        return [
            'id' => $this->entry_id,
            'title' => $entry?->get('title') ?? $this->entry_id,
            'entry_id' => $this->entry_id,
            'collection_handle' => $this->collection_handle,
            'site_handle' => $this->site_handle,
            'embedding_status' => EmbeddingStatus::fromChunks($this->embedded_chunks, $this->total_chunks)->value,
            'updated_at' => $this->updated_at,
        ];
    }

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make('title')->label(__('ai-entry-embeddings::frontend.embedding.columns.title'))->sortable(false),
            Column::make('entry_id')->label(__('ai-entry-embeddings::frontend.embedding.columns.entry_id'))->sortable(true),
            Column::make('site_handle')->label(__('ai-entry-embeddings::frontend.embedding.columns.site_handle'))->sortable(true),
            Column::make('embedding_status')->label(__('ai-entry-embeddings::frontend.embedding.columns.embedding_status'))->sortable(false),
            Column::make('updated_at')->label(__('ai-entry-embeddings::frontend.embedding.columns.updated_at'))->sortable(true),
        ];
    }
}
