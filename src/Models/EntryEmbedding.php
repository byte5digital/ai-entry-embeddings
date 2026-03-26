<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Models;

use Byte5\AiEntryEmbeddings\Enums\EmbeddingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $entry_id
 * @property string $collection_handle
 * @property string $site_handle
 * @property EmbeddingStatus $status
 * @property int $total_chunks
 * @property int $embedded_chunks
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class EntryEmbedding extends Model
{
    protected $table = 'ai_entry_embeddings';

    protected $guarded = [];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => EmbeddingStatus::class,
            'total_chunks' => 'integer',
            'embedded_chunks' => 'integer',
        ];
    }

    /** @return HasMany<EntryEmbeddingChunk, $this> */
    public function chunks(): HasMany
    {
        return $this->hasMany(EntryEmbeddingChunk::class);
    }
}
