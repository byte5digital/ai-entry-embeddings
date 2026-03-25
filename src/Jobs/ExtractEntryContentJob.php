<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Jobs;

use Byte5\AiEntryEmbeddings\Events\Extraction\ContentExtracted;
use Byte5\AiEntryEmbeddings\Events\Extraction\EmptyExtractionCompleted;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentExtractionPipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Statamic\Contracts\Entries\Entry as StatamicEntry;
use Throwable;

final class ExtractEntryContentJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 3;

    /** @var int[] */
    public array $backoff = [10, 60, 300];

    public function __construct(
        public StatamicEntry $entry,
    ) {}

    public function uniqueId(): string
    {
        return $this->entry->id().'_'.$this->entry->get('updated_at');
    }

    public function handle(ContentExtractionPipeline $pipeline): void
    {
        $payload = $pipeline->process($this->entry);

        if ($payload->getChunks() === []) {
            EmptyExtractionCompleted::dispatch($payload);

            return;
        }

        ContentExtracted::dispatch($payload);
    }

    public function failed(Throwable $exception): void {}
}
