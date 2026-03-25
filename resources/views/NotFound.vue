<script setup>
import { Head, Link } from '@statamic/cms/inertia';
import { CardPanel, Header } from '@statamic/cms/ui';

defineProps({
    title: String,
    description: String,
});
</script>

<template>
    <Head :title="__('ai-entry-embeddings::frontend.navigation.main.title')"/>
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entry-embeddings::frontend.navigation.main.title')" icon="ai-spark"/>
    </div>

    <CardPanel :heading="title">
        {{ description }}
    </CardPanel>
</template>
