<script setup>
import { Head } from '@statamic/cms/inertia';
import { Badge, Header, Listing } from '@statamic/cms/ui';

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
        <template #cell-embedding_status="{ value }">
            <Badge
                :color="value === 'generated' ? 'green' : value === 'partial' ? 'blue' : 'yellow'"
                :text="value"
            />
        </template>
    </Listing>
</template>
