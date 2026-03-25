<?php

declare(strict_types=1);

use Byte5\AiEntryEmbeddings\Http\Controllers\AiEntryEmbeddingsController;

Route::name('ai-entry-embeddings.')->prefix('ai-entry-embeddings')->group(function () {
    Route::get('/', [AiEntryEmbeddingsController::class, 'landingPage'])->name('landingPage');
    Route::get('generated-embeddings', [AiEntryEmbeddingsController::class, 'generatedEmbeddings'])->name('generatedEmbeddings');
});
