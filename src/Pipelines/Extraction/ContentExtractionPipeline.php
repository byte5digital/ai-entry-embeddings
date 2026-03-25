<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Pipelines\Extraction;

use Byte5\AiEntryEmbeddings\DTOs\CollectionConfig;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Fields\Field;

final readonly class ContentExtractionPipeline
{
    public function __construct(
        private FieldExtractorResolver $resolver,
        private EmbeddingCollectionRepositoryInterface $repository,
    ) {}

    public function process(StatamicEntry $entry): ExtractionPayload
    {
        $collectionHandle = $entry->collectionHandle();
        $collectionConfig = $this->repository->getConfig($collectionHandle);
        $siteHandle = $entry->site()?->handle() ?? 'default';

        $payload = new ExtractionPayload(
            entry: $entry,
            collectionHandle: $collectionHandle,
            siteHandle: $siteHandle,
            collectionConfig: $collectionConfig,
        );

        $fields = $this->resolveFieldsToExtract($entry, $collectionConfig);

        foreach ($fields as $fieldHandle => $fieldMeta) {
            $value = $entry->get($fieldHandle);
            if ($value === null) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            if ($value === []) {
                continue;
            }

            $extractor = $this->resolver->resolve(
                fieldType: $fieldMeta['type'],
                fieldHandle: $fieldHandle,
                customPipes: $fieldMeta['custom_pipes'],
            );

            if (! $extractor instanceof FieldExtractorInterface) {
                continue;
            }

            $chunks = $extractor->extract($entry, $fieldHandle, $value, $fieldMeta['field']);
            $payload->addChunks($chunks);
        }

        return $payload;
    }

    /**
     * Determine which fields to extract based on blueprint and collection config.
     *
     * @return array<string, array{type: string, field: Field, custom_pipes: array<int, class-string<FieldExtractorInterface>>}>
     */
    private function resolveFieldsToExtract(StatamicEntry $entry, CollectionConfig $config): array
    {
        $blueprint = $entry->blueprint();
        $allFields = $blueprint->fields()->all();
        $ignoredTypes = config('ai-entry-embeddings.extraction_pipeline.ignored_field_types', []);

        if ($config->fields === []) {
            return [];
        }

        $result = [];

        foreach ($allFields as $field) {
            $handle = $field->handle();
            $type = $field->type();

            if (in_array($type, $ignoredTypes, true)) {
                continue;
            }

            if (! in_array($handle, $config->fields, true)) {
                continue;
            }

            $result[$handle] = [
                'type' => $type,
                'field' => $field,
                'custom_pipes' => $config->customExtractors[$handle] ?? [],
            ];
        }

        return $result;
    }
}
