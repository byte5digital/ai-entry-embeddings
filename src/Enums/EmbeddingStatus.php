<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Enums;

enum EmbeddingStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Generated = 'generated';

    public static function fromChunks(int $embeddedChunks, int $totalChunks): self
    {
        if ($embeddedChunks === 0) {
            return self::Pending;
        }

        if ($embeddedChunks < $totalChunks) {
            return self::Partial;
        }

        return self::Generated;
    }
}
