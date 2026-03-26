<?php

declare(strict_types=1);

namespace Byte5\AiEntryEmbeddings\Jobs;

use Byte5\AiEntryEmbeddings\Enums\EmbeddingStatus;
use Byte5\AiEntryEmbeddings\Models\EntryEmbedding;
use Byte5\AiEntryEmbeddings\Services\Contracts\EntryEmbeddingServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Ai\Embeddings;
use Statamic\Entries\Entry as StatamicEntry;

final class GenerateEntryEmbeddingsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 3;

    /** @var int[] */
    public array $backoff = [10, 60, 300];

    public function __construct(
        public StatamicEntry $entry,
    ) {
        if ($connection = config('ai-entry-embeddings.queue.connection')) {
            $this->onConnection($connection);
        }

        if ($queue = config('ai-entry-embeddings.queue.name')) {
            $this->onQueue($queue);
        }
    }

    public function handle(EntryEmbeddingServiceInterface $service): void
    {
        $entryEmbedding = $service->findForEntry(
            $this->entry->id(),
            $this->entry->collectionHandle(),
        );

        if (! $entryEmbedding instanceof EntryEmbedding) {
            return;
        }

        $chunks = $entryEmbedding->chunks()->whereNull('embedding')->get();

        if ($chunks->isEmpty()) {
            $service->markChunksEmbedded($entryEmbedding, $entryEmbedding->total_chunks);

            return;
        }

        $texts = $chunks->pluck('content')->all();
        $dimensions = config('ai-entry-embeddings.embeddings.dimensions', 1536);

        $response = Embeddings::for($texts)
            ->dimensions($dimensions)
            ->generate();

        foreach ($chunks as $index => $chunk) {
            $chunk->update(['embedding' => $response->embeddings[$index]]);
        }

        $service->markChunksEmbedded($entryEmbedding, $entryEmbedding->total_chunks);
    }

    public function failed(\Throwable $exception): void
    {
        $entryEmbedding = EntryEmbedding::query()
            ->where('entry_id', $this->entry->id())
            ->where('collection_handle', $this->entry->collectionHandle())
            ->first();

        $entryEmbedding?->update(['status' => EmbeddingStatus::Failed]);
    }
}
