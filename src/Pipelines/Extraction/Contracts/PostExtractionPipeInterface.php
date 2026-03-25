<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ExtractionPayload;
use Closure;

interface PostExtractionPipeInterface
{
    public function handle(ExtractionPayload $payload, Closure $next): ExtractionPayload;
}
