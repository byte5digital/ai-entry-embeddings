<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings;

use Byte5\AiEntryEmbeddings\Events\Extraction\ContentExtracted;
use Byte5\AiEntryEmbeddings\Listeners\StoreExtractedChunksListener;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\ContentExtractionPipeline;
use Byte5\AiEntryEmbeddings\Pipelines\Extraction\FieldExtractorResolver;
use Byte5\AiEntryEmbeddings\Query\Scopes\Filters\EmbeddingSiteFilter;
use Byte5\AiEntryEmbeddings\Repositories\Contracts\EmbeddingCollectionRepositoryInterface;
use Byte5\AiEntryEmbeddings\Repositories\EmbeddingCollectionRepository;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Byte5\AiEntryEmbeddings\Services\EntryEmbeddingService;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

final class ServiceProvider extends AddonServiceProvider
{
    protected $scopes = [
        EmbeddingSiteFilter::class,
    ];

    protected $listen = [
        ContentExtracted::class => [
            StoreExtractedChunksListener::class,
        ],
    ];

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

        $this->app->singleton(FieldExtractorResolver::class);
        $this->app->singleton(ContentExtractionPipeline::class);
        $this->app->bind(EntryEmbeddingServiceInterface::class, EntryEmbeddingService::class);
        $this->app->singleton(EmbeddingCollectionRepositoryInterface::class, EmbeddingCollectionRepository::class);
    }

    private function bootNav(): void
    {
        NavAPI::extend(function (Nav $nav): void {
            $repository = $this->app->make(EmbeddingCollectionRepositoryInterface::class);

            $children = array_map(
                fn (string $handle) => $nav->item(ucfirst($handle))
                    ->route('ai-entry-embeddings.generatedEmbeddings', ['embeddingCollection' => $handle]),
                $repository->handles()
            );

            $nav->content(__('ai-entry-embeddings::frontend.navigation.main.title'))
                ->section('AI Tools')
                ->can('view AI entry embeddings')
                ->route('ai-entry-embeddings.landingPage')
                ->icon('ai-spark')
                ->children($children);
        });
    }

    private function bootPermissions(): void
    {
        Permission::register('view AI entry embeddings')
            ->label(__('ai-entry-embeddings::permission.view.title'));
    }
}
