<?php

declare(strict_types=1);

return [
    'navigation' => [
        'main' => [
            'title' => 'AI Entry Embeddings',
        ],
        'generated_embeddings' => [
            'title' => 'Generated Embeddings',
            'description' => 'View and manage AI-generated entry embeddings.',
        ],
        'entry_embedding_chunks' => [
            'title' => 'Entry Embedding Chunks',
        ],
    ],
    'collections' => [
        'no_config' => [
            'title' => 'No collections configured yet.',
            'description' => 'Add collections to the extraction pipeline in your config file to start generating embeddings.',
        ],
    ],
    'not_found' => [
        'collection' => [
            'title' => 'Collection not found.',
            'description' => 'The requested collection is not configured, or does not exist. Please check your configuration and try again.',
        ],
        'entry' => [
            'title' => 'Entry not found.',
            'description' => 'The requested entry does not exist, does not belong to this collection, or was not processed yet.',
        ],
    ],
    'embedding' => [
        'columns' => [
            'title' => 'Title',
            'entry_id' => 'Entry ID',
            'site_handle' => 'Site',
            'embedding_status' => 'Embedding',
            'updated_at' => 'Updated',
        ],
    ],
    'fieldtype' => [
        'title' => 'Embedding Status',
        'no_embeddings' => 'No embeddings generated yet. Embeddings will be created after the entry is saved.',
        'status' => [
            'pending' => 'Pending',
            'extracting' => 'Extracting...',
            'generating' => 'Generating embeddings...',
            'generated' => 'Generated',
            'failed' => 'Failed',
        ],
        'total_chunks' => 'Total Chunks',
        'embedded' => 'Embedded',
        'pending' => 'Pending',
        'last_updated' => 'Last updated',
        'view_details' => 'View chunk details',
    ],
    'chunk' => [
        'columns' => [
            'field_handle' => 'Field',
            'path' => 'Path',
            'content' => 'Content',
            'embedding_status' => 'Status',
            'metadata' => 'Metadata',
            'updated_at' => 'Updated',
        ],
    ],
];
