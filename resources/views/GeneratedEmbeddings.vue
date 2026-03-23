<script setup>
import { Head, Link } from '@statamic/cms/inertia';
import { Badge, Header, Listing } from '@statamic/cms/ui';

const props = defineProps({
    listingUrl: { type: String, required: true },
});
</script>

<template>
    <Head :title="__('ai-entry-embeddings::navigation.generated_embeddings.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entry-embeddings::navigation.generated_embeddings.title')">
        </Header>
    </div>
    <Listing
        :allowCustomizingColumns=false
        :url="listingUrl">
        <template #cell-processing_status="{ value }">
            <Badge
                :color="value === 'processed' ? 'green' : value === 'failed' ? 'red' : value === 'processing' ? 'blue' : 'yellow'"
                :text="value"
            />
        </template>
        <template #cell-entry_edit_url="{ value }">
            <Link v-if="value" :href="value"
                  class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                {{ __('View') }}
            </Link>
            <span v-else class="text-gray-400">&mdash;</span>
        </template>
    </Listing>
</template>
