<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import EventCard from '@/Components/EventCard.vue';
import { t } from '@/i18n';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    club: { type: Object, required: true },
    events: { type: Array, required: true },
});

const busy = ref(false);
const options = { preserveScroll: true, onStart: () => (busy.value = true), onFinish: () => (busy.value = false) };

const join = () => router.post(route('clubs.join', props.club.slug), {}, options);
const leave = () => {
    if (confirm(t('clubs.confirm_leave'))) router.delete(route('clubs.leave', props.club.slug), options);
};
const destroy = () => {
    if (confirm(t('common.confirm_delete'))) router.delete(route('clubs.destroy', props.club.slug));
};
</script>

<template>
    <Head :title="club.name" />

    <AppLayout>
        <template #header>
            <Link :href="route('clubs.index')" class="text-sm text-gray-500 hover:text-gray-800">← {{ $t('clubs.title') }}</Link>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h1 class="break-words text-2xl font-semibold text-gray-900">{{ club.name }}</h1>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('clubs.leader') }}: {{ club.leader ?? $t('clubs.no_leader') }} ·
                                {{ $t('clubs.members_count', { count: club.members_count }) }}
                            </p>
                            <span v-if="!club.is_active" class="mt-2 inline-block rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">{{ $t('clubs.inactive') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-if="club.is_member && club.can.leave"
                                type="button"
                                :disabled="busy"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                                @click="leave"
                            >
                                {{ $t('clubs.leave') }}
                            </button>
                            <button
                                v-else-if="club.can.join"
                                type="button"
                                :disabled="busy"
                                class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800 disabled:opacity-50"
                                @click="join"
                            >
                                {{ $t('clubs.join') }}
                            </button>
                            <Link v-if="club.can.update" :href="route('clubs.edit', club.slug)" class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $t('common.edit') }}</Link>
                            <button v-if="club.can.delete" type="button" class="rounded-md px-3 py-2 text-sm text-red-700 hover:bg-red-50" @click="destroy">{{ $t('common.delete') }}</button>
                        </div>
                    </div>
                    <p v-if="$page.props.auth.user.role === 'teacher'" class="mt-3 text-xs text-gray-500">{{ $t('clubs.teachers_view_only') }}</p>
                    <div v-if="club.description" class="mt-4 whitespace-pre-line break-words text-gray-700">{{ club.description }}</div>
                </section>

                <section class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $t('clubs.upcoming_events') }}</h2>
                        <Link
                            v-if="club.can.manage_events"
                            :href="route('events.create', { club: club.slug })"
                            class="rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                        >
                            {{ $t('events.new') }}
                        </Link>
                    </div>
                    <EmptyState v-if="events.length === 0" :title="$t('clubs.no_events')" />
                    <EventCard v-for="event in events" :key="event.id" :event="event" />
                </section>
            </div>
        </div>
    </AppLayout>
</template>
