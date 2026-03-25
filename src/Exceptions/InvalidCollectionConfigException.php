<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Exceptions;

use InvalidArgumentException;

final class InvalidCollectionConfigException extends InvalidArgumentException
{
    public static function missingFields(string $handle): self
    {
        return new self("Collection '{$handle}' is missing the required 'fields' key or it is not an array.");
    }

    public static function invalidFieldEntry(string $handle, int $index): self
    {
        return new self("Collection '{$handle}' has an invalid field entry at index {$index}. Each entry must be a string or a string key with an array of extractor class names.");
    }
}
