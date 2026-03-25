<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Listeners;

use Byte5\AiEntryEmbeddings\Jobs\ExtractEntryContentJob;
use Statamic\Events\EntrySaved;

final class EntryEventListener
{
    public function handleEntrySaved(EntrySaved $event): void
    {
        $entry = $event->entry;
        $collectionHandle = $entry->collectionHandle();

        $configuredCollections = array_keys(
            config('ai-entry-embeddings.extraction_pipeline.collections', [])
        );

        if (! in_array($collectionHandle, $configuredCollections, true)) {
            return;
        }

        $onlyPublished = config('ai-entry-embeddings.extraction_pipeline.only_published', true);

        if ($onlyPublished && ! $entry->published()) {
            return;
        }

        dispatch(new ExtractEntryContentJob($entry));
    }
}
