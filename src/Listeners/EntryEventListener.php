<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Listeners;

use Byte5\AiEntryEmbeddings\Jobs\ExtractEntryContentJob;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Statamic\Events\EntrySaved;

final readonly class EntryEventListener
{
    public function __construct(
        private EmbeddingCollectionRepositoryInterface $repository,
    ) {}

    public function handleEntrySaved(EntrySaved $event): void
    {
        $entry = $event->entry;
        $collectionHandle = $entry->collectionHandle();

        if (! $this->repository->exists($collectionHandle)) {
            return;
        }

        if ($this->repository->onlyPublished() && ! $entry->published()) {
            return;
        }

        dispatch(new ExtractEntryContentJob($entry));
    }
}
