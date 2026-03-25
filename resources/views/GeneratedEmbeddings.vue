<script setup>
import { Head, Link } from '@statamic/cms/inertia';
import { Badge, Header, Listing } from '@statamic/cms/ui';
import { formatDate } from '../js/formatDate';

const props = defineProps({
    listingUrl: { type: String, required: true },
    filters: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="__('ai-entry-embeddings::frontend.navigation.generated_embeddings.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entry-embeddings::frontend.navigation.generated_embeddings.title')">
        </Header>
    </div>
    <Listing
        :allowCustomizingColumns="false"
        :filters="filters"
        :url="listingUrl">
        <template #cell-title="{ row: entry }">
            <Link :href="entry.url" class="flex items-center gap-2 font-bold">
                {{ entry.title }}
            </Link>
        </template>
        <template #cell-embedding_status="{ value }">
            <Badge
                :color="value === 'generated' ? 'green' : value === 'partial' ? 'blue' : 'yellow'"
                :text="value"
            />
        </template>
        <template #cell-updated_at="{ value }">
            {{ formatDate(value) }}
        </template>
    </Listing>
</template>
