<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Ai;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Attributes\UseCheapestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseCheapestModel]
class ContentExtractionAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a content extraction agent. Your job is to extract meaningful, human-readable text from structured CMS field data.

        Rules:
        - Extract ONLY text content that a human would read on the page.
        - IGNORE presentation metadata: HTML tags (h1, h2, p, div), CSS classes (text-left, text-center), alignment values, style attributes, boolean flags (bold, italic), and any structural/layout configuration.
        - IGNORE technical identifiers: IDs, UUIDs, type keys, enabled/disabled flags.
        - Preserve the original language of the text. Do not translate.
        - Do not add, modify, or summarize the text. Return it exactly as it appears.
        - If the input contains multiple distinct text segments, return each as a separate chunk.
        - If there is no meaningful text content, return an empty chunks array.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'chunks' => $schema->array()->items(
                $schema->object([
                    'text' => $schema->string()->description('The extracted plain text content.'),
                ])
            ),
        ];
    }
}
