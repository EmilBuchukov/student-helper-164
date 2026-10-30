<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import EventCard from '@/Components/EventCard.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    events: { type: Object, required: true },
    filters: { type: Object, required: true },
    clubs: { type: Array, required: true },
    can: { type: Object, required: true },
});

const form = reactive({ ...props.filters });
const loading = ref(false);

const apply = () => {
    const query = {};
    if (form.club) query.club = form.club;
    if (form.past) query.past = 1;
    router.get(route('events.index'), query, {
        preserveState: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    });
};

const setPast = (value) => {
    form.past = value;
    apply();
};
</script>

<template>
    <Head :title="$t('events.title')" />

    <AppLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('events.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $t('events.intro') }}</p>
                </div>
                <Link v-if="can.create" :href="route('events.create')" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                    {{ $t('events.new') }}
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex rounded-md bg-gray-100 p-1">
                        <button
                            v-for="value in [false, true]"
                            :key="String(value)"
                            type="button"
                            class="rounded px-3 py-1 text-sm"
                            :class="form.past === value ? 'bg-white font-semibold shadow-sm' : 'text-gray-600'"
                            @click="setPast(value)"
                        >
                            {{ value ? $t('events.past') : $t('events.upcoming') }}
                        </button>
                    </div>
                    <label class="min-w-[12rem] flex-1 text-sm text-gray-700">
                        {{ $t('events.club') }}
                        <SelectInput v-model="form.club" class="mt-1 block w-full" @change="apply">
                            <option value="">{{ $t('events.all') }}</option>
                            <option value="school">{{ $t('events.school_wide') }}</option>
                            <option v-for="c in clubs" :key="c.slug" :value="c.slug">{{ c.name }}</option>
                        </SelectInput>
                    </label>
                </div>

                <p v-if="loading" class="text-sm text-gray-500">{{ $t('common.loading') }}</p>
                <EmptyState v-else-if="events.data.length === 0" :title="$t('events.empty')" />
                <div v-else class="space-y-3">
                    <EventCard v-for="event in events.data" :key="event.id" :event="event" />
                </div>

                <Pagination :links="events.links" />
            </div>
        </div>
    </AppLayout>
</template>
