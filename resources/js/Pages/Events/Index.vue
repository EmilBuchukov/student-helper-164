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
                    <h1 class="font-display text-title-1 font-semibold text-ink">{{ $t('events.title') }}</h1>
                    <p class="mt-1 text-sm text-ink-muted">{{ $t('events.intro') }}</p>
                </div>
                <Link v-if="can.create" :href="route('events.create')" class="rounded bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary-hover">
                    {{ $t('events.new') }}
                </Link>
            </div>
        </template>

        <div>
            <div class="mx-auto max-w-4xl space-y-4">
                <div class="flex flex-wrap items-end gap-3 rounded border border-line bg-surface p-4">
                    <div class="flex rounded bg-sunken p-1">
                        <button
                            v-for="value in [false, true]"
                            :key="String(value)"
                            type="button"
                            class="rounded px-3 py-1 text-sm"
                            :class="form.past === value ? 'border border-line bg-surface font-semibold ' : 'text-ink-muted'"
                            @click="setPast(value)"
                        >
                            {{ value ? $t('events.past') : $t('events.upcoming') }}
                        </button>
                    </div>
                    <label class="min-w-[12rem] flex-1 text-sm text-ink-muted">
                        {{ $t('events.club') }}
                        <SelectInput v-model="form.club" class="mt-1 block w-full" @change="apply">
                            <option value="">{{ $t('events.all') }}</option>
                            <option value="school">{{ $t('events.school_wide') }}</option>
                            <option v-for="c in clubs" :key="c.slug" :value="c.slug">{{ c.name }}</option>
                        </SelectInput>
                    </label>
                </div>

                <p v-if="loading" class="text-sm text-ink-subtle">{{ $t('common.loading') }}</p>
                <EmptyState v-else-if="events.data.length === 0" :title="$t('events.empty')" />
                <div v-else class="space-y-3">
                    <EventCard v-for="event in events.data" :key="event.id" :event="event" />
                </div>

                <Pagination :links="events.links" />
            </div>
        </div>
    </AppLayout>
</template>
