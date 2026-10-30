<script setup>
import { formatDateTime } from '@/format';
import { Link } from '@inertiajs/vue3';

defineProps({
    event: { type: Object, required: true },
});
</script>

<template>
    <Link :href="route('events.show', event.id)" class="flex gap-4 rounded-lg bg-white p-4 shadow-sm transition hover:shadow">
        <div class="w-16 shrink-0 rounded-md bg-red-50 py-2 text-center text-red-800">
            <div class="text-xl font-bold leading-none">{{ new Date(event.starts_at).getDate() }}</div>
            <div class="mt-1 text-xs uppercase">{{ new Date(event.starts_at).toLocaleDateString('bg-BG', { month: 'short' }) }}</div>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase text-red-700">{{ event.club ? event.club.name : $t('events.school_wide') }}</p>
            <p class="mt-1 break-words font-semibold text-gray-900">{{ event.title }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ formatDateTime(event.starts_at) }} · {{ event.location }}</p>
            <p class="mt-2 flex flex-wrap gap-x-3 text-xs text-gray-500">
                <span>{{ $t('events.going_count', { count: event.going_count }) }}</span>
                <span>{{ $t('events.interested_count', { count: event.interested_count }) }}</span>
                <span v-if="event.my_rsvp" class="font-medium text-green-700">✓ {{ $t(`events.rsvp.${event.my_rsvp}`) }}</span>
            </p>
        </div>
    </Link>
</template>
