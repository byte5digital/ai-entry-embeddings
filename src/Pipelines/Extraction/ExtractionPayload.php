<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction;

use Statamic\Contracts\Entries\Entry;

final class ExtractionPayload
{
    /** @var ContentChunk[] */
    private array $chunks = [];

    /**
     * @param  array<string, mixed>  $collectionConfig
     */
    public function __construct(
        public readonly Entry $entry,
        public readonly string $collectionHandle,
        public readonly string $siteHandle,
        public readonly array $collectionConfig,
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
