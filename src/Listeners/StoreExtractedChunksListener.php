<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Listeners;

use Byte5\AiEntryEmbeddings\Events\Extraction\ContentExtracted;
use Byte5\AiEntryEmbeddings\Jobs\GenerateEntryEmbeddingsJob;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;

final class StoreExtractedChunksListener
{
    public function __construct(
        private readonly EntryEmbeddingServiceInterface $service,
    ) {}

    public function handle(ContentExtracted $event): void
    {
        $payload = $event->payload;

        $this->service->replaceForEntry(
            entryId: $payload->entry->id(),
            collectionHandle: $payload->collectionHandle,
            siteHandle: $payload->siteHandle,
            chunks: $payload->getChunks(),
        );

        GenerateEntryEmbeddingsJob::dispatch($payload->entry);
    }
}
