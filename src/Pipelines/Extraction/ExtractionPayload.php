<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction;

use Byte5\AiEntryEmbeddings\DTOs\CollectionConfig;
use Statamic\Entries\Entry as StatamicEntry;

final class ExtractionPayload
{
    /** @var ContentChunk[] */
    private array $chunks = [];

    public function __construct(
        public readonly StatamicEntry $entry,
        public readonly string $collectionHandle,
        public readonly string $siteHandle,
        public readonly CollectionConfig $collectionConfig,
    ) {}

    public function addChunk(ContentChunk $chunk): void
    {
        $this->chunks[] = $chunk;
    }

    /**
     * @param  ContentChunk[]  $chunks
     */
    public function addChunks(array $chunks): void
    {
        array_push($this->chunks, ...$chunks);
    }

    /**
     * @return ContentChunk[]
     */
    public function getChunks(): array
    {
        return $this->chunks;
    }
}
