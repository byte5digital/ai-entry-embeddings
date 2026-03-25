<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Statamic\CP\Column;

final class EmbeddingCollectionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'handle' => $this->resource['handle'],
            'title' => $this->resource['title'],
            'url' => $this->resource['url'],
            'entries_count' => $this->resource['entries_count'],
            'total_chunks' => $this->resource['total_chunks'],
            'embedded_chunks' => $this->resource['embedded_chunks'],
            'pending_chunks' => $this->resource['pending_chunks'],
        ];
    }

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make('title')->label(__('Collection')),
            Column::make('entries_count')->label(__('Entries')),
            Column::make('embeddings')->label(__('Embeddings')),
        ];
    }
}
