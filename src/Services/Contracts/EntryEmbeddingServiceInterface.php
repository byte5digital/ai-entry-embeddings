<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Services\Contracts;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;

interface EntryEmbeddingServiceInterface
{
    /**
     * Replace all chunks for an entry with new ones.
     *
     * @param  ContentChunk[]  $chunks
     */
    public function replaceForEntry(
        string $entryId,
        string $collectionHandle,
        string $siteHandle,
        array $chunks,
    ): void;
}
