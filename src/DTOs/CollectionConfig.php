<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\DTOs;

use Byte5\AiEntryEmbeddings\Exceptions\InvalidCollectionConfigException;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;

final readonly class CollectionConfig
{
    /**
     * @param  string  $handle  The collection handle.
     * @param  list<string>  $fields  Field handles to extract.
     * @param  array<string, list<class-string<FieldExtractorInterface>>>  $customExtractors  Field handle → extractor class names.
     */
    public function __construct(
        public string $handle,
        public array $fields,
        public array $customExtractors = [],
    ) {}

    /**
     * @param  array<string, mixed>  $config  Raw config array from the config file.
     *
     * @throws InvalidCollectionConfigException
     */
    public static function fromArray(string $handle, array $config): self
    {
        if (! isset($config['fields']) || ! is_array($config['fields'])) {
            throw InvalidCollectionConfigException::missingFields($handle);
        }

        $fields = [];
        $customExtractors = [];

        foreach ($config['fields'] as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $fields[] = $value;
            } elseif (is_string($key) && is_array($value)) {
                $fields[] = $key;
                $customExtractors[$key] = $value;
            } else {
                throw InvalidCollectionConfigException::invalidFieldEntry($handle, is_int($key) ? $key : count($fields));
            }
        }

        return new self(
            handle: $handle,
            fields: $fields,
            customExtractors: $customExtractors,
        );
    }
}
