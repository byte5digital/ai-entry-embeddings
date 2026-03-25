<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Concerns\ConvertsHtmlToPlainText;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Statamic\Contracts\Entries\Entry;
use Statamic\Facades\Markdown;
use Statamic\Fields\Field;

final class ExtractMarkdownField implements FieldExtractorInterface
{
    use ConvertsHtmlToPlainText;

    public function extract(Entry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $html = Markdown::parse($value);
        $text = $this->htmlToPlainText($html);

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
