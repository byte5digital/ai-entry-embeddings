# AI Entry Embeddings

AI Entry Embeddings is a Statamic addon that automatically extracts content from entries, splits it into meaningful chunks with metadata, and generates vector embeddings using [Laravel AI SDK](https://laravel.com/docs/master/ai-sdk). Designed for building RAG (Retrieval-Augmented Generation) search experiences on top of your Statamic content.

## How It Works

When an entry is saved in Statamic:

1. The **extraction pipeline** walks through configured fields, producing `ContentChunk` objects -- each with the extracted text, the originating field handle, a dot-notation path (e.g. `page_builder.pricing_block.0`), and structured metadata.
2. Chunks are stored in the `ai_entry_embeddings` PostgreSQL table, replacing any previous chunks for that entry.
3. A queued job calls the Laravel AI SDK to generate vector embeddings for each chunk and writes them back to the database.

The result is a table of chunk-level embeddings you can query with pgvector for similarity search, knowing exactly which section of which entry matched.

## Requirements

- PHP 8.3+
- Statamic 6.0+
- PostgreSQL with the [pgvector](https://github.com/pgvector/pgvector) extension
- A configured embedding provider via [Laravel AI](https://github.com/laravel/ai) (e.g. OpenAI)

## Installation

```bash
composer require byte5/ai-entry-embeddings
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=config --provider="Byte5\AiEntryEmbeddings\ServiceProvider"
```

Run migrations (this will enable the pgvector extension and create the `ai_entry_embeddings` table):

```bash
php artisan migrate
```

## Configuration

The configuration file is published to `config/ai-entry-embeddings.php`.

### Collections and Fields

You **must** explicitly list which collections and fields to extract. Nothing is extracted by default -- this prevents accidentally exposing sensitive data to the AI.

```php
'collections' => [
    'pages' => [
        'fields' => ['title', 'page_builder'],
    ],
    'blog' => [
        'fields' => ['title', 'content'],
    ],
],
```

To use a custom extractor for a specific field, pass an array of extractor classes:

```php
'collections' => [
    'pages' => [
        'fields' => [
            'title',
            'custom_field' => [\App\Extractors\MyCustomExtractor::class],
        ],
    ],
],
```

### Embedding Dimensions

```php
'embeddings' => [
    'dimensions' => 1536,
],
```

This value is used both during migration (to size the database column) and at runtime (when calling the embedding API). Changing it after the initial migration requires a new migration to alter the column size, and all existing embeddings must be regenerated since vectors of different dimensions are incompatible.

### Only Published

When enabled (default), draft entries are skipped:

```php
'only_published' => true,
```

### Field Type Extractors

The addon ships with extractors for common Statamic field types:

| Field Type   | Extractor                  | Behavior                                              |
|--------------|----------------------------|-------------------------------------------------------|
| `text`       | `ExtractTextField`         | Returns the raw string value                          |
| `textarea`   | `ExtractTextField`         | Returns the raw string value                          |
| `markdown`   | `ExtractMarkdownField`     | Converts Markdown to HTML, then strips to plain text  |
| `bard`       | `ExtractBardField`         | Splits prose and sets into separate chunks             |
| `replicator` | `ExtractReplicatorField`   | One chunk per set, with nested field extraction        |
| `grid`       | `ExtractGridField`         | One chunk per row, with nested column extraction       |
| `select`     | `ExtractSelectField`       | Returns the option label(s)                            |

You can override or add mappings in the `default_field_extractors` config key.

Field types listed in `ignored_field_types` (e.g. `toggle`, `assets`, `date`) are never extracted.

## Extending

### Custom Field Extractor

Implement `FieldExtractorInterface` and return an array of `ContentChunk` objects:

```php
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentChunk;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\Contracts\FieldExtractorInterface;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Fields\Field;

class ExtractMyField implements FieldExtractorInterface
{
    public function extract(
        StatamicEntry $entry,
        string $fieldHandle,
        mixed $value,
        Field $field,
        string $parentPath = '',
    ): array {
        $path = $parentPath !== '' ? "{$parentPath}.{$fieldHandle}" : $fieldHandle;

        return [
            new ContentChunk(
                text: (string) $value,
                fieldHandle: $fieldHandle,
                path: $path,
                metadata: ['field_handle' => $fieldHandle],
            ),
        ];
    }
}
```

Register it either globally in config:

```php
'default_field_extractors' => [
    'my_type' => \App\Extractors\ExtractMyField::class,
],
```

Or per-field in a collection:

```php
'collections' => [
    'pages' => [
        'fields' => [
            'my_field' => [\App\Extractors\ExtractMyField::class],
        ],
    ],
],
```

### Events

| Event                      | When                                    | Payload              |
|----------------------------|-----------------------------------------|----------------------|
| `ContentExtracted`         | Chunks were successfully extracted       | `ExtractionPayload`  |
| `EmptyExtractionCompleted` | Extraction completed with zero chunks    | `ExtractionPayload`  |

## Control Panel

The addon registers a navigation section under **AI Tools** in the Statamic control panel, with a landing page and an embeddings overview. Access is gated by the `view AI entry embeddings` permission.

## License

This addon is open-sourced software licensed under the GNU General Public License v3.0 (GPL-3.0). See [LICENSE](LICENSE) for details.
