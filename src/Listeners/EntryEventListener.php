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
        dispatch(new ExtractEntryContentJob($entry));
    }
}
