<script setup>
import { formatDateTime } from '@/format';

defineProps({
    logs: { type: Array, required: true },
});

const detailText = (details) =>
    Object.entries(details)
        .filter(([, v]) => v !== null && v !== false && v !== '')
        .map(([k, v]) => `${k}: ${v === true ? '✓' : v}`)
        .join(' · ');
</script>

<template>
    <div>
        <p v-if="logs.length === 0" class="text-sm text-gray-500">{{ $t('admin.audit.empty') }}</p>
        <ul v-else class="divide-y divide-gray-100">
            <li v-for="log in logs" :key="log.id" class="py-2 text-sm">
                <div class="flex flex-wrap items-baseline gap-x-2">
                    <span class="font-medium text-gray-900">{{ log.actor ?? $t('admin.audit.system') }}</span>
                    <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700">{{ $t(`admin.audit.actions.${log.action}`) }}</span>
                    <span v-if="log.label" class="break-words text-gray-700">„{{ log.label }}“</span>
                    <span class="ms-auto text-xs text-gray-500">{{ formatDateTime(log.created_at) }}</span>
                </div>
                <p v-if="detailText(log.details)" class="mt-0.5 break-words text-xs text-gray-500">{{ detailText(log.details) }}</p>
            </li>
        </ul>
    </div>
</template>
