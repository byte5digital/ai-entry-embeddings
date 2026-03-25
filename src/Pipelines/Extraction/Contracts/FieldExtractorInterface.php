<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Fields\Field;

interface FieldExtractorInterface
{
    /**
     * Extract content from a field's value as discrete chunks with metadata.
     *
     * @param  string  $parentPath  Dot-notation path of the parent context (empty for top-level fields).
     * @return ContentChunk[]
     */
    public function extract(StatamicEntry $entry, string $fieldHandle, mixed $value, Field $field, string $parentPath = ''): array;
}
