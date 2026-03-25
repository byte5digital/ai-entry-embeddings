<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Repositories\Contracts;

use Byte5\AiEntryEmbeddings\DTOs\CollectionConfig;
use Byte5\AiEntryEmbeddings\Exceptions\InvalidCollectionConfigException;

interface EmbeddingCollectionRepositoryInterface
{
    /**
     * Get all configured collection handles.
     *
     * @return list<string>
     */
    public function handles(): array;

    /**
     * Check whether a collection handle is configured.
     */
    public function exists(string $handle): bool;

    /**
     * Get the parsed config for a specific collection handle.
     *
     * @throws InvalidCollectionConfigException
     */
    public function getConfig(string $handle): CollectionConfig;

    /**
     * Whether only published entries should be processed.
     */
    public function onlyPublished(): bool;
}
