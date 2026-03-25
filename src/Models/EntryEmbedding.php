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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read int $entries_count
 * @property-read int $total_chunks
 * @property-read int $embedded_chunks
 * @property-read int $pending_chunks
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

    public function getPendingChunksAttribute(): int
    {
        return ($this->total_chunks ?? 0) - ($this->embedded_chunks ?? 0);
    }

}
