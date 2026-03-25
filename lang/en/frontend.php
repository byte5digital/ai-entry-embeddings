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
    ]
];
