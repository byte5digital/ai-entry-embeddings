<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Listeners;

use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Statamic\Events\EntryBlueprintFound;

final readonly class EntryBlueprintFoundListener
{
    public function __construct(
        private EmbeddingCollectionRepositoryInterface $repository,
    ) {}

    public function handle(EntryBlueprintFound $event): void
    {
        if (! request()->routeIs('statamic.cp.*')) {
            return;
        }

        $collection = $event->entry?->collectionHandle();

        if ($collection === null || ! $this->repository->exists($collection)) {
            return;
        }

        $event->blueprint->ensureField('embedding_status', [
            'type' => 'embedding_status',
            'display' => __('ai-entry-embeddings::frontend.fieldtype.title'),
            'listable' => 'hidden',
        ], 'Embeddings');
    }
}
