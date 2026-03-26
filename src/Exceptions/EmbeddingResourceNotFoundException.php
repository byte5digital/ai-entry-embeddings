<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Exceptions;

use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

final class EmbeddingResourceNotFoundException extends RuntimeException
{
    public function __construct(
        private readonly string $titleKey = 'ai-entry-embeddings::frontend.not_found.collection.title',
        private readonly string $descriptionKey = 'ai-entry-embeddings::frontend.not_found.collection.description',
    ) {
        parent::__construct();
    }

    public static function collection(): self
    {
        return new self(
            titleKey: 'ai-entry-embeddings::frontend.not_found.collection.title',
            descriptionKey: 'ai-entry-embeddings::frontend.not_found.collection.description',
        );
    }

    public static function entry(): self
    {
        return new self(
            titleKey: 'ai-entry-embeddings::frontend.not_found.entry.title',
            descriptionKey: 'ai-entry-embeddings::frontend.not_found.entry.description',
        );
    }

    public function render(): Response
    {
        return Inertia::render('ai-entry-embeddings::NotFound', [
            'title' => __($this->titleKey),
            'description' => __($this->descriptionKey),
        ]);
    }
}
