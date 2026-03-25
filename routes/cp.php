<?php

declare(strict_types=1);

use Byte5\AiEntryEmbeddings\Http\Controllers\AiEntryEmbeddingsController;

Route::name('ai-entry-embeddings.')->prefix('ai-entry-embeddings')->group(function () {
    Route::get('/', [AiEntryEmbeddingsController::class, 'landingPage'])->name('landingPage');
    Route::get('/collections/list', [AiEntryEmbeddingsController::class, 'listCollections'])->name('collections.list');
    Route::get('/{embeddingCollection}', [AiEntryEmbeddingsController::class, 'generatedEmbeddings'])->name('generatedEmbeddings');
    Route::get('/{embeddingCollection}/list', [AiEntryEmbeddingsController::class, 'listEmbeddings'])->name('embeddings.list');
    Route::get('/{embeddingCollection}/{embeddingEntryId}', [AiEntryEmbeddingsController::class, 'entryEmbeddingChunks'])->name('entryEmbeddingChunks');
    Route::get('/{embeddingCollection}/{embeddingEntryId}/list', [AiEntryEmbeddingsController::class, 'listEntryEmbeddingChunks'])->name('entryEmbeddingChunks.list');
});
