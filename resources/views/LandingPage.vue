<script setup>
import {Head, Link} from '@statamic/cms/inertia';
import {CardPanel, Header, Heading, Subheading} from '@statamic/cms/ui';

const props = defineProps({
    entries: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head :title="__('ai-entry-embeddings::navigation.main.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entry-embeddings::navigation.main.title')" icon="ai-spark">
        </Header>
    </div>
    <CardPanel>
        <div class="flex flex-wrap starting-style-transition-children">
            <Link
                v-for="entry in entries"
                :key="entry.url"
                :href="entry.url"
                class="group w-full items-start rounded-md border border-transparent px-2 py-3 lg:p-4 hover:bg-gray-100 dark:hover:bg-gray-800 md:flex lg:w-1/2"
            >
                <div v-if="entry.icon" class="size-6 text-gray-400 mt-1 mb-2 me-4" v-html="entry.icon"/>
                <div class="flex-1 md:me-6">
                    <Heading :text="entry.title" size="lg"/>
                    <Subheading v-text="entry.description"/>
                </div>
            </Link>
        </div>
    </CardPanel>
</template>

<style scoped>

</style>
