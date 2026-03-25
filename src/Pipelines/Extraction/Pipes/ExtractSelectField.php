<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Fields\Field;

final class ExtractSelectField implements FieldExtractorInterface
{
    public function extract(StatamicEntry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        if (in_array($value, [null, '', []], true)) {
            return [];
        }

        $options = $field->fieldtype()->config('options', []);
        $values = is_array($value) ? $value : [$value];

        $labels = [];

        foreach ($values as $v) {
            $labels[] = $options[$v] ?? (string) $v;
        }

        $text = implode(', ', array_filter($labels));

        if (trim($text) === '') {
            return [];
        }

        $path = $parentPath !== '' ? sprintf('%s.%s', $parentPath, $fieldHandle) : $fieldHandle;

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
