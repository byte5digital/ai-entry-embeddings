<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes;

use Byte5\AiEntryEmbeddings\Ai\ContentExtractionAgent;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Fields\Field;

final readonly class ExtractFieldWithAi implements FieldExtractorInterface
{
    public function __construct(
        private ContentExtractionAgent $agent,
    ) {}

    public function extract(StatamicEntry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array
    {
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($encoded === false || $encoded === 'null') {
            return [];
        }

        $prompt = sprintf(
            "Extract the text content from the following CMS field data.\n\nField handle: %s\nField type: %s\n\nData:\n%s",
            $fieldHandle,
            $field->type(),
            $encoded,
        );

        $response = $this->agent->prompt($prompt);

        $basePath = $parentPath !== '' ? sprintf('%s.%s', $parentPath, $fieldHandle) : $fieldHandle;

        return $this->parseResponse($response->toArray(), $fieldHandle, $basePath);
    }

    /**
     * @param  array{chunks: array<int, array{text: string}>}  $structured
     * @return ContentChunk[]
     */
    private function parseResponse(array $structured, string $fieldHandle, string $basePath): array
    {
        $chunks = [];

        foreach ($structured['chunks'] ?? [] as $index => $item) {
            $text = trim($item['text'] ?? '');

            if ($text === '') {
                continue;
            }

            $chunks[] = new ContentChunk(
                text: $text,
                fieldHandle: $fieldHandle,
                path: sprintf('%s.ai.%d', $basePath, $index),
                metadata: [
                    'field_handle' => $fieldHandle,
                    'extraction_method' => 'ai',
                ],
            );
        }

        return $chunks;
    }
}
