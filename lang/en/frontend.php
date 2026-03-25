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
    ],
    'collections'=>[
        'no_config'=>[
            'title'=>'No collections configured yet.',
            'description'=>'Add collections to the extraction pipeline in your config file to start generating embeddings.',
        ]
    ],
    'not_found' => [
        'title' => 'Collection not found.',
        'description' => 'The requested collection is not configured, or does not exist. Please check your configuration and try again.',
        'back' => 'Back to safety',
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
];
