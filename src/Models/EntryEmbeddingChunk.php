<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $entry_embedding_id
 * @property string $field_handle
 * @property string $path
 * @property string $content
 * @property array<int, float>|null $embedding
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class EntryEmbeddingChunk extends Model
{
    protected $table = 'ai_entry_embedding_chunks';

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'metadata' => 'array',
        ];
    }

    /** @return BelongsTo<EntryEmbedding, $this> */
    public function entryEmbedding(): BelongsTo
    {
        return $this->belongsTo(EntryEmbedding::class);
    }
}
