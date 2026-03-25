<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Concerns\ConvertsHtmlToPlainText;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\FieldExtractorResolver;
use Statamic\Contracts\Entries\Entry;
use Statamic\Fields\Field;
use Statamic\Fields\Fields;

final readonly class ExtractBardField implements FieldExtractorInterface
{
    use ConvertsHtmlToPlainText;

    public function __construct(
        private FieldExtractorResolver $resolver,
    ) {}

    public function extract(Entry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        $basePath = $parentPath !== '' ? sprintf('%s.%s', $parentPath, $fieldHandle) : $fieldHandle;

        if (is_string($value)) {
            $text = $this->htmlToPlainText($value);

            if (trim($text) === '') {
                return [];
            }

            return [
                new ContentChunk(
                    text: $text,
                    fieldHandle: $fieldHandle,
                    path: $basePath,
                    metadata: ['field_handle' => $fieldHandle],
                ),
            ];
        }

        if (! is_array($value)) {
            return [];
        }

        $chunks = [];
        $proseParts = [];
        $proseIndex = 0;

        foreach ($value as $node) {
            $type = $node['type'] ?? null;

            if ($type === 'set') {
                // Flush accumulated prose as a chunk before the set
                $this->flushProse($proseParts, $chunks, $fieldHandle, $basePath, $proseIndex);
                $proseParts = [];

                // Extract the set as its own chunk(s)
                $setChunks = $this->extractFromSet($entry, $node, $field, $basePath);
                array_push($chunks, ...$setChunks);

                $proseIndex++;
            } else {
                $text = $this->extractTextFromNode($node);
                if (trim($text) !== '') {
                    $proseParts[] = $text;
                }
            }
        }

        // Flush remaining prose
        $this->flushProse($proseParts, $chunks, $fieldHandle, $basePath, $proseIndex);

        return $chunks;
    }

    /**
     * Flush accumulated prose parts into a single chunk.
     *
     * @param  string[]  $proseParts
     * @param  ContentChunk[]  $chunks
     */
    private function flushProse(array $proseParts, array &$chunks, string $fieldHandle, string $basePath, int $proseIndex): void
    {
        if ($proseParts === []) {
            return;
        }

        $text = implode(' ', $proseParts);

        if (trim($text) === '') {
            return;
        }

        $chunks[] = new ContentChunk(
            text: $text,
            fieldHandle: $fieldHandle,
            path: sprintf('%s.prose.%d', $basePath, $proseIndex),
            metadata: [
                'field_handle' => $fieldHandle,
                'node_type' => 'prose',
            ],
        );
    }

    /**
     * Recursively extract text from a ProseMirror node tree.
     */
    private function extractTextFromNode(array $node): string
    {
        $text = '';

        if (isset($node['text'])) {
            $text .= $node['text'];
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                $childText = $this->extractTextFromNode($child);
                if ($childText !== '') {
                    $text .= ($text !== '' ? ' ' : '').$childText;
                }
            }
        }

        return trim($text);
    }

    /**
     * Extract chunks from a Bard set by recursively resolving extractors for its fields.
     *
     * Simple sub-fields are merged into one chunk for the set.
     * Compound sub-fields produce their own chunks.
     *
     * @return ContentChunk[]
     */
    private function extractFromSet(Entry $entry, array $node, Field $parentField, string $basePath): array
    {
        $setValues = $node['attrs']['values'] ?? [];
        $setType = $setValues['type'] ?? null;

        if ($setType === null) {
            return [];
        }

        $enabled = $setValues['enabled'] ?? true;
        if (! $enabled) {
            return [];
        }

        $setsConfig = $parentField->fieldtype()->flattenedSetsConfig();
        $setConfig = $setsConfig[$setType] ?? null;

        if ($setConfig === null || ! isset($setConfig['fields'])) {
            return [];
        }

        $setPath = sprintf('%s.%s', $basePath, $setType);
        $mergedParts = [];
        $subChunks = [];

        // Resolve field references (e.g., "common.text_rich") into proper Field objects
        $resolvedFields = (new Fields($setConfig['fields']))->all();

        foreach ($resolvedFields as $setField) {
            $setFieldHandle = $setField->handle();
            $setFieldValue = $setValues[$setFieldHandle] ?? null;
            if ($setFieldValue === null) {
                continue;
            }
            if ($setFieldValue === '') {
                continue;
            }
            if ($setFieldValue === []) {
                continue;
            }

            $setFieldType = $setField->type();

            $extractor = $this->resolver->resolve($setFieldType, $setFieldHandle);

            if (!$extractor instanceof FieldExtractorInterface) {
                if (is_string($setFieldValue)) {
                    $mergedParts[] = $setFieldValue;
                }

                continue;
            }

            $extractedChunks = $extractor->extract($entry, $setFieldHandle, $setFieldValue, $setField, $setPath);

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
                fieldHandle: $parentField->handle(),
                path: $setPath,
                metadata: [
                    'field_handle' => $parentField->handle(),
                    'set_type' => $setType,
                ],
            );
        }

        array_push($result, ...$subChunks);

        return $result;
    }
}
