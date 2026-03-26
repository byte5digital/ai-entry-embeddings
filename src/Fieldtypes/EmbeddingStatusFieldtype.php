<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Fieldtypes;

use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Statamic\Entries\Entry;
use Statamic\Fields\Fieldtype;

final class EmbeddingStatusFieldtype extends Fieldtype
{
    /** @var list<string> */
    protected $categories = ['special'];

    /** @var bool */
    protected $selectable = false;

    /** @var bool */
    protected $localizable = false;

    /** @var bool */
    protected $validatable = false;

    /** @var bool */
    protected $defaultable = false;

    /** @return array<string, mixed> */
    public function preload(): array
    {
        $parent = $this->field()->parent();

        if (! $parent instanceof Entry || $parent->id() === null) {
            return ['has_embeddings' => false, 'is_processing' => false];
        }

        $service = app(EntryEmbeddingServiceInterface::class);
        $entryEmbedding = $service->findForEntry($parent->id(), $parent->collectionHandle());

        if ($entryEmbedding === null) {
            return ['has_embeddings' => false, 'is_processing' => false];
        }

        $isProcessing = in_array($entryEmbedding->status->value, ['extracting', 'generating'], true);

        return [
            'has_embeddings' => true,
            'is_processing' => $isProcessing,
            'total_chunks' => $entryEmbedding->total_chunks,
            'embedded_chunks' => $entryEmbedding->embedded_chunks,
            'pending_chunks' => $entryEmbedding->total_chunks - $entryEmbedding->embedded_chunks,
            'status' => $entryEmbedding->status->value,
            'updated_at' => $entryEmbedding->updated_at?->toIso8601String(),
            'detail_url' => cp_route('ai-entry-embeddings.entryEmbeddingChunks', [
                'embeddingCollection' => $parent->collectionHandle(),
                'embeddingEntryId' => $parent->id(),
            ]),
        ];
    }

    public function preProcess(mixed $data): mixed
    {
        return null;
    }

    public function process(mixed $data): mixed
    {
        return null;
    }
}
