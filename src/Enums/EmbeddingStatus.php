<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Enums;

enum EmbeddingStatus: string
{
    case Pending = 'pending';
    case Extracting = 'extracting';
    case Generating = 'generating';
    case Generated = 'generated';
    case Failed = 'failed';
}
