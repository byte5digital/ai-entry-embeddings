<script setup>
import { Head } from '@statamic/cms/inertia';
import { Badge, Header, Listing } from '@statamic/cms/ui';
import { formatDate } from '../js/formatDate';

const props = defineProps({
    listingUrl: { type: String, required: true },
    embeddingCollection: { type: String, required: true },
    embeddingEntryId: { type: String, required: true },
    entryTitle: { type: String, required: true },
});
</script>

<template>
    <Head :title="entryTitle + ' — ' + __('ai-entry-embeddings::frontend.navigation.entry_embedding_chunks.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="entryTitle">
        </Header>
    </div>
    <Listing
        :allowCustomizingColumns="false"
        :url="listingUrl">
        <template #cell-content="{ value }">
            <span class="truncate max-w-xs block" :title="value">
                {{ value }}
            </span>
        </template>
        <template #cell-embedding_status="{ value }">
            <Badge
                :color="value === 'generated' ? 'green' : 'yellow'"
                :text="value"
            />
        </template>
        <template #cell-updated_at="{ value }">
            {{ formatDate(value) }}
        </template>
        <template #cell-metadata="{ value }">
            <span v-if="value" class="text-xs font-mono text-gray-500">
                {{ JSON.stringify(value) }}
            </span>
            <span v-else class="text-gray-400">&mdash;</span>
        </template>
    </Listing>
</template>
