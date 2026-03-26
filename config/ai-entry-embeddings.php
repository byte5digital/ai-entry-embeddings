<?php

declare(strict_types=1);

use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractBardField;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractGridField;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractMarkdownField;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractReplicatorField;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractSelectField;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractTextField;

return [
    'extraction_pipeline' => [
        /*
        |--------------------------------------------------------------------------
        | Default Field Extractors
        |--------------------------------------------------------------------------
        |
        | Maps Statamic field types to their extractor class. Each extractor
        | implements FieldExtractorInterface and converts the field's value
        | to plain text. You can override or extend this map.
        |
        | To use the AI-powered extractor for a field type, replace the
        | default extractor with \Byte5\AiEntryEmbeddings\Pipelines\Extraction\Pipes\ExtractFieldWithAi::class.
        | This will use an AI agent to extract text from the field, which can be more effective for complex or nested content.
        |
        |   'bard' => ExtractFieldWithAi::class,
        |   'replicator' => ExtractFieldWithAi::class,
        |
        */
        'default_field_extractors' => [
            'text' => ExtractTextField::class,
            'textarea' => ExtractTextField::class,
            'markdown' => ExtractMarkdownField::class,
            'bard' => ExtractBardField::class,
            'replicator' => ExtractReplicatorField::class,
            'grid' => ExtractGridField::class,
            'select' => ExtractSelectField::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | Ignored Field Types
        |--------------------------------------------------------------------------
        |
        | Field types listed here are never extracted, regardless of collection
        | configuration. These are types that don't produce useful text for
        | embedding purposes.
        |
        */
        'ignored_field_types' => [
            'toggle', 'color', 'assets', 'hidden', 'revealer',
            'spacer', 'section', 'icon', 'video', 'width',
            'slug', 'template', 'date', 'time', 'integer',
            'floatval', 'range', 'button_group', 'select',
        ],

        /*
        |--------------------------------------------------------------------------
        | Only Published
        |--------------------------------------------------------------------------
        |
        | When true, only published entries will be processed. Draft entries
        | will be skipped to avoid embedding in-progress content.
        |
        */
        'only_published' => true,

        /*
        |--------------------------------------------------------------------------
        | Collections
        |--------------------------------------------------------------------------
        |
        | Define which collections should have their entries extracted.
        | Only collections listed here will be processed.
        |
        | You MUST explicitly list which fields to extract. If 'fields' is
        | missing or empty, nothing will be extracted for that collection.
        | This prevents accidentally exposing sensitive data.
        |
        | Example:
        |   'pages' => [
        |       'fields' => ['title', 'page_builder'],
        |   ],
        |
        | Custom extractor per field:
        |   'pages' => [
        |       'fields' => [
        |           'title',
        |           'custom_field' => [\App\Pipes\MyCustomExtractor::class],
        |       ],
        |   ],
        |
        | AI-powered extractor per field (uses an AI agent instead of
        | rule-based parsing):
        |   'pages' => [
        |       'fields' => [
        |           'title',
        |           'page_builder' => [ExtractFieldWithAi::class],
        |       ],
        |   ],
        |
        */
        'collections' => [
            'pages' => [
                'fields' => ['title', 'page_builder'],
            ],
        ],
    ],

    /*
      |--------------------------------------------------------------------------
      | Keep Deleted Entry Embeddings
      |--------------------------------------------------------------------------
      |
      | When false, embeddings are automatically deleted when their source entry
      | is deleted from Statamic. Set to true to preserve embeddings even after
      | the entry is removed.
      |
    */
    'keep_deleted_entry_embeddings' => false,

    /*
    |--------------------------------------------------------------------------
    | Embeddings
    |--------------------------------------------------------------------------
    |
    | Configuration for vector embedding generation.
    |
    | The dimensions value is used during migration to define the database
    | column size. Changing this value after migration requires a new
    | migration to alter the column, and all existing embeddings must be
    | regenerated (old vectors are incompatible with a new dimension size).
    |
    */
    'embeddings' => [
        'dimensions' => 1536,
    ],

    /*
   |--------------------------------------------------------------------------
   | Queue
   |--------------------------------------------------------------------------
   |
   | The queue connection and queue name used for dispatching extraction
   | and embedding jobs. Set to null to use the application defaults.
   |
   */
    'queue' => [
        'connection' => 'redis',
        'name' => 'embeddings',
    ],
];
