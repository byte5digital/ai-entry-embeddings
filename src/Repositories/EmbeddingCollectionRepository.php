<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Repositories;

use Byte5\AiEntryEmbeddings\DTOs\CollectionConfig;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

final readonly class EmbeddingCollectionRepository implements EmbeddingCollectionRepositoryInterface
{
    public function __construct(
        private ConfigRepository $config,
    ) {}

    /** {@inheritDoc} */
    public function handles(): array
    {
        return array_values(array_filter(
            array_keys($this->config->get('ai-entry-embeddings.extraction_pipeline.collections', [])),
            'is_string'
        ));
    }

    /** {@inheritDoc} */
    public function exists(string $handle): bool
    {
        return array_key_exists(
            $handle,
            $this->config->get('ai-entry-embeddings.extraction_pipeline.collections', [])
        );
    }

    /** {@inheritDoc} */
    public function getConfig(string $handle): CollectionConfig
    {
        $raw = $this->config->get(
            'ai-entry-embeddings.extraction_pipeline.collections.'.$handle,
            []
        );

        return CollectionConfig::fromArray($handle, $raw);
    }

    /** {@inheritDoc} */
    public function onlyPublished(): bool
    {
        return (bool) $this->config->get(
            'ai-entry-embeddings.extraction_pipeline.only_published',
            true
        );
    }

    /** {@inheritDoc} */
    public function keepDeletedEntryEmbeddings(): bool
    {
        return (bool) $this->config->get(
            'ai-entry-embeddings.keep_deleted_entry_embeddings',
            false
        );
    }
}
