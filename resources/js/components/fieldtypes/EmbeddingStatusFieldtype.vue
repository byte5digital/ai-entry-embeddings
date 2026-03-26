<template>
    <div class="embedding-status-fieldtype">
        <div v-if="meta?.has_embeddings" class="space-y-4">
            <div class="flex items-center gap-2">
                <Badge :color="statusColor" :text="statusLabel" :icon="meta.is_processing ? 'loading' : undefined" />
                <span v-if="meta.updated_at" class="text-xs text-gray-500">
                    {{ __('ai-entry-embeddings::frontend.fieldtype.last_updated') }}: {{ formatDate(meta.updated_at) }}
                </span>
            </div>

            <div v-if="meta.total_chunks > 0" class="grid grid-cols-3 gap-4">
                <CardPanel :heading="__('ai-entry-embeddings::frontend.fieldtype.total_chunks')">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ meta.total_chunks }}</div>
                </CardPanel>
                <CardPanel :heading="__('ai-entry-embeddings::frontend.fieldtype.embedded')">
                    <div class="text-2xl font-bold text-green-600">{{ meta.embedded_chunks }}</div>
                </CardPanel>
                <CardPanel :heading="__('ai-entry-embeddings::frontend.fieldtype.pending')">
                    <div class="text-2xl font-bold text-amber-600">{{ meta.pending_chunks }}</div>
                </CardPanel>
            </div>
          <Button v-if="meta.detail_url && meta.total_chunks > 0" :href="meta.detail_url" icon-append="arrow-right">
            {{ __('ai-entry-embeddings::frontend.fieldtype.view_details') }}
          </Button>
        </div>

        <p v-else class="text-sm text-gray-500">
            {{ __('ai-entry-embeddings::frontend.fieldtype.no_embeddings') }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Badge, CardPanel, Button } from '@statamic/cms/ui';
import { formatDate } from '../../formatDate.js';

const props = defineProps({
    meta: { type: Object, default: null },
});

const __ = window.__;

const statusLabel = computed(() => {
    const key = `ai-entry-embeddings::frontend.fieldtype.status.${props.meta?.status}`;
    return __(key) ?? props.meta?.status;
});

const statusColor = computed(() => {
    const map = {
        pending: 'default',
        extracting: 'blue',
        generating: 'blue',
        generated: 'green',
        failed: 'red',
    };
    return map[props.meta?.status] ?? 'default';
});
</script>
