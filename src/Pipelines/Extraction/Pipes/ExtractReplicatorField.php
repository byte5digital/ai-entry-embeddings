<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\FieldExtractorResolver;
use Statamic\Contracts\Entries\Entry;
use Statamic\Fields\Field;
use Statamic\Fields\Fields;

final class ExtractReplicatorField implements FieldExtractorInterface
{
    public function __construct(
        private readonly FieldExtractorResolver $resolver,
    ) {}

    public function extract(Entry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        if (! is_array($value)) {
            return [];
        }

        $setsConfig = $field->fieldtype()->flattenedSetsConfig();
        $basePath = $parentPath !== '' ? "{$parentPath}.{$fieldHandle}" : $fieldHandle;
        $chunks = [];

        foreach ($value as $setIndex => $set) {
            if (! is_array($set)) {
                continue;
            }

            $enabled = $set['enabled'] ?? true;
            if (! $enabled) {
                continue;
            }

            $setType = $set['type'] ?? null;
            if ($setType === null || ! isset($setsConfig[$setType])) {
                continue;
            }

            $setPath = "{$basePath}.{$setType}.{$setIndex}";
            $setFieldDefinitions = $setsConfig[$setType]['fields'] ?? [];

            // Resolve field references (e.g., "common.text_rich") into proper Field objects
            $resolvedFields = (new Fields($setFieldDefinitions))->all();

            $setChunks = $this->extractSet($entry, $fieldHandle, $set, $resolvedFields, $setPath, $setType, $setIndex);
            array_push($chunks, ...$setChunks);
        }

        return $chunks;
    }

    /**
     * Extract chunks from a single replicator set.
     *
     * Simple sub-fields (returning 1 chunk) are merged into one chunk for the set.
     * Compound sub-fields (returning multiple chunks) are kept as separate chunks.
     *
     * @param  array<string, mixed>  $set
     * @param  \Illuminate\Support\Collection<string, Field>  $resolvedFields
     * @return ContentChunk[]
     */
    private function extractSet(
        Entry $entry,
        string $fieldHandle,
        array $set,
        $resolvedFields,
        string $setPath,
        string $setType,
        int $setIndex,
    ): array {
        $mergedParts = [];
        $subChunks = [];

        foreach ($resolvedFields as $setField) {
            $setFieldHandle = $setField->handle();
            $setFieldValue = $set[$setFieldHandle] ?? null;

            if ($setFieldValue === null || $setFieldValue === '' || $setFieldValue === []) {
                continue;
            }

            $setFieldType = $setField->type();

            $extractor = $this->resolver->resolve($setFieldType, $setFieldHandle);

            if ($extractor === null) {
                if (is_string($setFieldValue)) {
                    $mergedParts[] = $setFieldValue;
                }

                continue;
            }

            $extractedChunks = $extractor->extract($entry, $setFieldHandle, $setFieldValue, $setField, $setPath);

            if (count($extractedChunks) === 1) {
                // Simple field — merge its text into the set chunk
                $mergedParts[] = $extractedChunks[0]->text;
            } else {
                // Compound field — keep its chunks separate with accumulated paths
                array_push($subChunks, ...$extractedChunks);
            }
        }

        $result = [];

        $mergedText = implode(' ', array_filter($mergedParts, fn (string $text): bool => trim($text) !== ''));

        if (trim($mergedText) !== '') {
            $result[] = new ContentChunk(
                text: $mergedText,
                fieldHandle: $fieldHandle,
                path: $setPath,
                metadata: [
                    'field_handle' => $fieldHandle,
                    'set_type' => $setType,
                    'set_index' => $setIndex,
                ],
            );
        }

        array_push($result, ...$subChunks);

        return $result;
    }
}
