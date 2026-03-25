<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::ensureVectorExtensionExists();

        Schema::create('ai_entry_embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('entry_id')->index()->comment('Statamic entry identifier');
            $table->string('collection_handle')->index()->comment('Statamic collection handle, e.g. "pages"');
            $table->string('site_handle')->comment('Statamic site handle, e.g. "default"');
            $table->string('field_handle')->comment('Top-level field handle, e.g. "page_builder"');
            $table->string('path')->comment('Dot-notation chunk origin, e.g. "page_builder.pricing_block.0"');
            $table->text('content')->comment('Extracted plain text content for this chunk');
            $dimensions = config('ai-entry-embeddings.embeddings.dimensions', 1536);
            $table->vector('embedding', $dimensions)->nullable()->index()->comment('Vector embedding, populated by embedding generation job');
            $table->json('metadata')->nullable()->comment('Structured metadata from extraction (set_type, set_index, etc.)');
            $table->timestamps();

            $table->index(['entry_id', 'collection_handle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_entry_embeddings');
    }
};
