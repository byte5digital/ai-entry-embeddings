<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Events\Extraction;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ExtractionPayload;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class EmptyExtractionCompleted
{
    use Dispatchable;

    public function __construct(
        public ExtractionPayload $payload,
    ) {}
}
