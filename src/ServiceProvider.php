<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings;

use Byte5\AiEntryEmbeddings\Ai\Agents\Rag\ChatAgent;
use Byte5\AiEntryEmbeddings\Ai\Agents\Simplifier\EntrySimplifierAgent;
use Byte5\AiEntryEmbeddings\Ai\Contracts\ChatAgentInterface;
use Byte5\AiEntryEmbeddings\Ai\Contracts\EntrySimplifierAgentInterface;
use Byte5\AiEntryEmbeddings\Ai\Pipelines\RagPipeline;
use Byte5\AiEntryEmbeddings\Services\ContentChunker;
use Byte5\AiEntryEmbeddings\Services\EmbeddingService;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

final class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon(): void
    {
        $this->publishes([
            __DIR__.'/../config/ai-entry-embeddings.php' => config_path('ai-entry-embeddings.php'),
        ], 'config');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'ai-entry-embeddings');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->bootNav();
        $this->bootPermissions();
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/ai-entry-embeddings.php', 'ai-entry-embeddings');
    }

    private function bootNav(): void
    {
        NavAPI::extend(fn (Nav $nav) => $nav
            ->content(__('ai-entry-embeddings::navigation.main.title'))
            ->section('AI Tools')
            ->can('view AI entry embeddings')
            ->route('ai-entry-embeddings.landingPage')
            ->icon('ai-spark')
            ->children([
                $nav->item(__('ai-entry-embeddings::navigation.generated_embeddings.title'))->route('ai-entry-embeddings.generatedEmbeddings'),
            ])
        );
    }

    private function bootPermissions(): void
    {
        Permission::register('view AI entry embeddings')
            ->label(__('ai-entry-embeddings::permission.view.title'));
    }
}
