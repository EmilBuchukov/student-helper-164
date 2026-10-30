<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDateTime } from '@/format';
import { t } from '@/i18n';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: { type: Object, required: true },
    statuses: { type: Array, required: true },
});

const busy = ref(false);
const options = { preserveScroll: true, onStart: () => (busy.value = true), onFinish: () => (busy.value = false) };

const rsvp = (status) => router.post(route('events.rsvp', props.event.id), { status }, options);
const cancel = () => router.delete(route('events.rsvp.cancel', props.event.id), options);
const destroy = () => {
    if (confirm(t('common.confirm_delete'))) router.delete(route('events.destroy', props.event.id));
};
</script>

<template>
    <Head :title="event.title" />

    <AppLayout>
        <template #header>
            <Link :href="event.club ? route('clubs.show', event.club.slug) : route('events.index')" class="text-sm text-gray-500 hover:text-gray-800">
                ← {{ event.club ? event.club.name : $t('events.title') }}
            </Link>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <article class="space-y-5 rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase text-red-700">{{ event.club ? event.club.name : $t('events.school_wide') }}</p>
                        <h1 class="mt-1 break-words text-2xl font-semibold text-gray-900">{{ event.title }}</h1>
                    </div>

                    <dl class="grid gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-gray-500">{{ $t('events.starts_at') }}</dt>
                            <dd class="font-medium text-gray-900">
                                {{ formatDateTime(event.starts_at) }}<template v-if="event.ends_at"> – {{ formatDateTime(event.ends_at) }}</template>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">{{ $t('events.location') }}</dt>
                            <dd class="break-words font-medium text-gray-900">{{ event.location }}</dd>
                        </div>
                    </dl>

                    <div v-if="event.description" class="whitespace-pre-line break-words text-gray-700">{{ event.description }}</div>

                    <p class="flex flex-wrap gap-x-4 text-sm text-gray-600">
                        <span>{{ $t('events.going_count', { count: event.going_count }) }}</span>
                        <span>{{ $t('events.interested_count', { count: event.interested_count }) }}</span>
                        <span v-if="event.creator">{{ $t('events.created_by', { name: event.creator }) }}</span>
                    </p>

                    <div v-if="event.can.rsvp" class="flex flex-wrap items-center gap-2 border-t pt-4">
                        <span class="text-sm text-gray-600">{{ $t('events.your_rsvp') }}:</span>
                        <button
                            v-for="status in statuses"
                            :key="status"
                            type="button"
                            :disabled="busy"
                            :aria-pressed="event.my_rsvp === status"
                            class="rounded-full px-4 py-1.5 text-sm font-medium disabled:opacity-50"
                            :class="event.my_rsvp === status ? 'bg-red-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            @click="rsvp(status)"
                        >
                            {{ $t(`events.rsvp.${status}`) }}
                        </button>
                        <button v-if="event.my_rsvp" type="button" :disabled="busy" class="text-sm text-gray-500 hover:text-gray-800" @click="cancel">
                            {{ $t('events.rsvp_cancel') }}
                        </button>
                    </div>

                    <div v-if="event.can.update || event.can.delete" class="flex gap-2 border-t pt-4">
                        <Link v-if="event.can.update" :href="route('events.edit', event.id)" class="rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $t('common.edit') }}</Link>
                        <button v-if="event.can.delete" type="button" class="rounded-md px-3 py-2 text-sm text-red-700 hover:bg-red-50" @click="destroy">{{ $t('common.delete') }}</button>
                    </div>
                </article>
            </div>
        </div>
    </AppLayout>
</template>
