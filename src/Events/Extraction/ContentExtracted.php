<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Events\Extraction;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class ContentExtracted
{
    use Dispatchable;

    public function __construct() {}
}
