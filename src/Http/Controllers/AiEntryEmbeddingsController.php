<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class AiEntryEmbeddingsController extends CpController
{
    public function landingPage(): Response
    {
        return Inertia::render('ai-entry-embeddings::LandingPage', [
            'entries' => [
                [
                    'title' => __('ai-entry-embeddings::navigation.generated_embeddings.title'),
                    'description' => __('ai-entry-embeddings::navigation.generated_embeddings.description'),
                    'url' => cp_route('ai-entry-embeddings.generatedEmbeddings'),
                ],
            ],
        ]);
    }

    public function generatedEmbeddings(): Response
    {
        return Inertia::render('ai-entry-embeddings::GeneratedEmbeddings', [
        ]);
    }
}
