<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $entry_id
 * @property string $collection_handle
 * @property string $site_handle
 * @property string $field_handle
 * @property string $path
 * @property string $content
 * @property array|null $embedding
 * @property array|null $metadata
 */
class EntryEmbedding extends Model
{
    use HasFactory;
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
