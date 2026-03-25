<script setup>
import { Head, Link } from '@statamic/cms/inertia';
import { Badge, CardPanel, EmptyStateItem, Header, Listing } from '@statamic/cms/ui';

const props = defineProps({
    collections: {
        type: Array,
        default: () => [],
    },
    columns: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head :title="__('ai-entry-embeddings::frontend.navigation.main.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entry-embeddings::frontend.navigation.main.title')" icon="ai-spark"/>
    </div>

    <Listing
        v-if="collections.length"
        :items="collections"
        :columns="columns"
        :allowSearch="false"
        :allowCustomizingColumns="false"
    >
        <template #cell-title="{ row: collection }">
            <Link :href="collection.url" class="flex items-center gap-2">
                {{ collection.title }}
            </Link>
        </template>
        <template #cell-embeddings="{ row: collection }">
            <div class="flex items-center gap-2">
                <Badge
                    v-if="collection.embedded_chunks > 0"
                    color="green"
                    :text="__('Embedded')"
                    :append="collection.embedded_chunks"
                    pill
                />
                <Badge
                    v-if="collection.pending_chunks > 0"
                    color="yellow"
                    :text="__('Pending')"
                    :append="collection.pending_chunks"
                    pill
                />
                <Badge
                    v-if="collection.total_chunks === 0"
                    :text="__('No chunks')"
                    pill
                />
            </div>
        </template>
    </Listing>

    <CardPanel v-else :heading="__('ai-entry-embeddings::frontend.collections.no_config.title')">
        {{ __('ai-entry-embeddings::frontend.collections.no_config.description') }}
    </CardPanel>
</template>
