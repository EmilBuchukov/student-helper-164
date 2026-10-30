<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    clubs: { type: Array, required: true },
    can: { type: Object, required: true },
});
</script>

<template>
    <Head :title="$t('clubs.title')" />

    <AppLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('clubs.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $t('clubs.intro') }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('events.index')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        {{ $t('events.title') }}
                    </Link>
                    <Link v-if="can.create" :href="route('clubs.create')" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                        {{ $t('clubs.new') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <EmptyState v-if="clubs.length === 0" :title="$t('clubs.empty')" />

                <ul v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <li v-for="club in clubs" :key="club.id">
                        <Link :href="route('clubs.show', club.slug)" class="flex h-full flex-col rounded-lg bg-white p-5 shadow-sm transition hover:shadow" :class="{ 'opacity-60': !club.is_active }">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="break-words font-semibold text-gray-900">{{ club.name }}</h3>
                                <span v-if="club.is_member" class="shrink-0 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ $t('clubs.member_badge') }}</span>
                                <span v-else-if="!club.is_active" class="shrink-0 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">{{ $t('clubs.inactive') }}</span>
                            </div>
                            <p class="mt-2 flex-1 break-words text-sm text-gray-600">{{ club.excerpt }}</p>
                            <p class="mt-4 text-xs text-gray-500">
                                {{ club.leader ?? $t('clubs.no_leader') }} · {{ $t('clubs.members_count', { count: club.members_count }) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">{{ $t('clubs.upcoming_count', { count: club.upcoming_events_count }) }}</p>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
