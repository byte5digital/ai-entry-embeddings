<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\FieldExtractorResolver;
use Statamic\Contracts\Entries\Entry;
use Statamic\Fields\Field;
use Statamic\Fields\Fields;

final class ExtractGridField implements FieldExtractorInterface
{
    public function __construct(
        private readonly FieldExtractorResolver $resolver,
    ) {}

    public function extract(Entry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        if (! is_array($value)) {
            return [];
        }

        $basePath = $parentPath !== '' ? "{$parentPath}.{$fieldHandle}" : $fieldHandle;
        $columnsConfig = $field->fieldtype()->config('fields') ?? [];

        // Resolve field references into proper Field objects
        $resolvedColumns = (new Fields($columnsConfig))->all();
        $chunks = [];

        foreach ($value as $rowIndex => $row) {
            if (! is_array($row)) {
                continue;
            }

            $rowPath = "{$basePath}.row.{$rowIndex}";
            $rowChunks = $this->extractRow($entry, $fieldHandle, $row, $resolvedColumns, $rowPath, $rowIndex);
            array_push($chunks, ...$rowChunks);
        }

        return $chunks;
    }

    /**
     * Extract chunks from a single grid row.
     *
     * Simple sub-fields are merged into one chunk for the row.
     * Compound sub-fields produce their own chunks.
     *
     * @param  array<string, mixed>  $row
     * @param  \Illuminate\Support\Collection<string, Field>  $resolvedColumns
     * @return ContentChunk[]
     */
    private function extractRow(
        Entry $entry,
        string $fieldHandle,
        array $row,
        $resolvedColumns,
        string $rowPath,
        int $rowIndex,
    ): array {
        $mergedParts = [];
        $subChunks = [];

        foreach ($resolvedColumns as $columnField) {
            $columnHandle = $columnField->handle();
            $cellValue = $row[$columnHandle] ?? null;

            if ($cellValue === null || $cellValue === '' || $cellValue === []) {
                continue;
            }

            $columnType = $columnField->type();

            $extractor = $this->resolver->resolve($columnType, $columnHandle);

            if ($extractor === null) {
                if (is_string($cellValue)) {
                    $mergedParts[] = $cellValue;
                }

                continue;
            }

            $extractedChunks = $extractor->extract($entry, $columnHandle, $cellValue, $columnField, $rowPath);

            if (count($extractedChunks) === 1) {
                $mergedParts[] = $extractedChunks[0]->text;
            } else {
                array_push($subChunks, ...$extractedChunks);
            }
        }

        $result = [];

        $mergedText = implode(' ', array_filter($mergedParts, fn (string $text): bool => trim($text) !== ''));

        if (trim($mergedText) !== '') {
            $result[] = new ContentChunk(
                text: $mergedText,
                fieldHandle: $fieldHandle,
                path: $rowPath,
                metadata: [
                    'field_handle' => $fieldHandle,
                    'row_index' => $rowIndex,
                ],
            );
        }

        array_push($result, ...$subChunks);

        return $result;
    }
}
