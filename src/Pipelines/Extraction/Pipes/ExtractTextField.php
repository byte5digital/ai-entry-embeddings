<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Statamic\Contracts\Entries\Entry;
use Statamic\Fields\Field;

final class ExtractTextField implements FieldExtractorInterface
{
    public function extract(Entry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return [];
        }

        $text = (string) $value;

        if (trim($text) === '') {
            return [];
        }

        $path = $parentPath !== '' ? "{$parentPath}.{$fieldHandle}" : $fieldHandle;

        return [
            new ContentChunk(
                text: $text,
                fieldHandle: $fieldHandle,
                path: $path,
                metadata: ['field_handle' => $fieldHandle],
            ),
        ];
    }
}
