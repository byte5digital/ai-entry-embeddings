<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $entry_id
 * @property string $collection_handle
 * @property string $site_handle
 * @property string $field_handle
 * @property string $path
 * @property string $content
 * @property array<int, float>|null $embedding
 * @property array<string, mixed>|null $metadata
 */
class EntryEmbedding extends Model
{
    protected $table = 'ai_entry_embeddings';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'metadata' => 'array',
        ];
    }
}
