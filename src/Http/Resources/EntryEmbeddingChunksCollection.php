<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class EntryEmbeddingChunksCollection extends ResourceCollection
{
    /** @var class-string */
    public $collects = EntryEmbeddingChunkResource::class;

    /** @return array<string, mixed> */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'columns' => EntryEmbeddingChunkResource::columns(),
                'activeFilterBadges' => $this->additional['activeFilterBadges'] ?? [],
            ],
        ];
    }
}
