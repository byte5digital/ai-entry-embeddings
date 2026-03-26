<template>
    <div class="embedding-status-fieldtype">
        <div v-if="meta?.has_embeddings" class="space-y-4">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="statusClasses"
                >
                    {{ statusLabel }}
                </span>
                <span v-if="meta.updated_at" class="text-xs text-gray-500">
                    Last updated: {{ formatDate(meta.updated_at) }}
                </span>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-lg bg-gray-100 p-3 text-center dark:bg-gray-800">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ meta.total_chunks }}</div>
                    <div class="text-xs text-gray-500">Total Chunks</div>
                </div>
                <div class="rounded-lg bg-gray-100 p-3 text-center dark:bg-gray-800">
                    <div class="text-2xl font-bold text-green-600">{{ meta.embedded_chunks }}</div>
                    <div class="text-xs text-gray-500">Embedded</div>
                </div>
                <div class="rounded-lg bg-gray-100 p-3 text-center dark:bg-gray-800">
                    <div class="text-2xl font-bold text-amber-600">{{ meta.pending_chunks }}</div>
                    <div class="text-xs text-gray-500">Pending</div>
                </div>
            </div>

            <a
                v-if="meta.detail_url"
                :href="meta.detail_url"
                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline dark:text-blue-400"
            >
                View chunk details &rarr;
            </a>
        </div>

        <p v-else class="text-sm text-gray-500">
            No embeddings generated yet. Embeddings will be created after the entry is saved.
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDate } from '../../formatDate.js';

const props = defineProps({
    meta: { type: Object, default: null },
});

const statusLabel = computed(() => {
    const labels = {
        generated: 'Generated',
        partial: 'Partial',
        pending: 'Pending',
    };
    return labels[props.meta?.status] ?? props.meta?.status;
});

const statusClasses = computed(() => {
    const map = {
        generated: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        partial: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
    };
    return map[props.meta?.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
});
</script>
