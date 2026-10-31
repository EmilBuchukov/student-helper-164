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
                    <h1 class="font-display text-title-1 font-semibold text-ink">{{ $t('clubs.title') }}</h1>
                    <p class="mt-1 text-sm text-ink-muted">{{ $t('clubs.intro') }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('events.index')" class="rounded border border-line-strong border border-line bg-surface px-4 py-2 text-sm font-semibold text-ink-muted hover:bg-sunken">
                        {{ $t('events.title') }}
                    </Link>
                    <Link v-if="can.create" :href="route('clubs.create')" class="rounded bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary-hover">
                        {{ $t('clubs.new') }}
                    </Link>
                </div>
            </div>
        </template>

        <div>
            <div class="">
                <EmptyState v-if="clubs.length === 0" :title="$t('clubs.empty')" />

                <ul v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <li v-for="club in clubs" :key="club.id">
                        <Link :href="route('clubs.show', club.slug)" class="flex h-full flex-col rounded border border-line bg-surface p-5 transition" :class="{ 'opacity-60': !club.is_active }">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="break-words font-semibold text-ink">{{ club.name }}</h3>
                                <span v-if="club.is_member" class="shrink-0 rounded-full bg-success-soft px-2 py-0.5 text-xs font-medium text-success">{{ $t('clubs.member_badge') }}</span>
                                <span v-else-if="!club.is_active" class="shrink-0 rounded-full bg-sunken px-2 py-0.5 text-xs text-ink-muted">{{ $t('clubs.inactive') }}</span>
                            </div>
                            <p class="mt-2 flex-1 break-words text-sm text-ink-muted">{{ club.excerpt }}</p>
                            <p class="mt-4 text-xs text-ink-subtle">
                                {{ club.leader ?? $t('clubs.no_leader') }} · {{ $t('clubs.members_count', { count: club.members_count }) }}
                            </p>
                            <p class="mt-1 text-xs text-ink-subtle">{{ $t('clubs.upcoming_count', { count: club.upcoming_events_count }) }}</p>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
