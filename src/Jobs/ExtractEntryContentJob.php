<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Jobs;

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
       public StatamicEntry $entry
    ) {}


    public function handle(): void
    {
        dd($this->entry);
    }

    public function failed(Throwable $exception): void
    {

    }
}
