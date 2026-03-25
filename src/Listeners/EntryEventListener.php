<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Listeners;

use Byte5\AiEntryEmbeddings\Jobs\ExtractEntryContentJob;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Events\Dispatcher;
use Statamic\Events\EntryDeleted;
use Statamic\Events\EntrySaved;

final readonly class EntryEventListener
{
    public function __construct(
        private EmbeddingCollectionRepositoryInterface $repository,
        private EntryEmbeddingServiceInterface $service,
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

    public function handleEntryDeleted(EntryDeleted $event): void
    {
        if ($this->repository->keepDeletedEntryEmbeddings()) {
            return;
        }

        $entry = $event->entry;

        if (! $this->repository->exists($entry->collectionHandle())) {
            return;
        }

        $this->service->deleteForEntry($entry->id());
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(EntrySaved::class, [self::class, 'handleEntrySaved']);
        $events->listen(EntryDeleted::class, [self::class, 'handleEntryDeleted']);
    }
}
