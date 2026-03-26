<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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

    /** @return Attribute<int, never> */
    protected function pendingChunks(): Attribute
    {
        return Attribute::get(fn (): int => ($this->total_chunks ?? 0) - ($this->embedded_chunks ?? 0));
    }
}
