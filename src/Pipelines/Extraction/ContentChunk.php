<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction;

final readonly class ContentChunk
{
    /**
     * @param  string  $text  Extracted plain text for this chunk.
     * @param  string  $fieldHandle  Top-level field handle (e.g., 'page_builder').
     * @param  string  $path  Dot-notation origin path (e.g., 'page_builder.pricing_block.0').
     * @param  array<string, mixed>  $metadata  Structured metadata (set_type, set_index, etc.).
     */
    public function __construct(
        public string $text,
        public string $fieldHandle,
        public string $path,
        public array $metadata = [],
    ) {}
}
