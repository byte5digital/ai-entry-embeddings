<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction;

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Illuminate\Contracts\Container\Container;

final readonly class FieldExtractorResolver
{
    public function __construct(
        private Container $container,
    ) {}

    /**
     * Resolve the appropriate extractor for a given field type.
     *
     * @param  array<int, class-string<FieldExtractorInterface>>  $customPipes
     */
    public function resolve(string $fieldType, string $fieldHandle, array $customPipes = []): ?FieldExtractorInterface
    {
        if ($customPipes !== []) {
            return $this->container->make($customPipes[0]);
        }

        $extractors = config('ai-entry-embeddings.extraction_pipeline.default_field_extractors', []);

        if (! isset($extractors[$fieldType])) {
            return null;
        }

        return $this->container->make($extractors[$fieldType]);
    }
}
