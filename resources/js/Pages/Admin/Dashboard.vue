<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AuditList from '@/Pages/Admin/Audit/AuditList.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    counts: { type: Object, required: true },
    recent: { type: Array, required: true },
});

const cards = [
    { key: 'open_reports', route: 'moderation.reports.index', highlight: true },
    { key: 'active_mutes', route: 'moderation.reports.index' },
    { key: 'users', route: 'admin.users.index' },
    { key: 'students', route: 'admin.users.index', query: { role: 'student' } },
    { key: 'suspended', route: 'admin.users.index', query: { status: 'suspended' } },
    { key: 'pending_whitelist', route: 'admin.whitelist.index', query: { status: 'pending' } },
    { key: 'exams', route: 'exams.index' },
    { key: 'questions', route: 'questions.index' },
];

const links = [
    { label: 'nav.moderation_reports', route: 'moderation.reports.index' },
    { label: 'nav.admin_users', route: 'admin.users.index' },
    { label: 'nav.admin_whitelist', route: 'admin.whitelist.index' },
    { label: 'nav.admin_import', route: 'admin.students.import' },
    { label: 'nav.admin_deleted', route: 'admin.deleted.index' },
    { label: 'nav.admin_audit', route: 'admin.audit.index' },
];
</script>

<template>
    <Head :title="$t('admin.dashboard.title')" />

    <AppLayout>
        <template #header>
            <h1 class="font-display text-title-1 font-semibold text-ink">{{ $t('admin.dashboard.title') }}</h1>
            <p class="mt-1 text-sm text-ink-muted">{{ $t('admin.dashboard.intro') }}</p>
        </template>

        <div>
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <Link
                        v-for="card in cards"
                        :key="card.key"
                        :href="route(card.route, card.query ?? {})"
                        class="rounded border border-line bg-surface p-4 transition"
                        :class="{ 'ring-2 ring-red-500': card.highlight && counts[card.key] > 0 }"
                    >
                        <p class="text-2xl font-bold text-ink">{{ counts[card.key] }}</p>
                        <p class="mt-1 text-sm text-ink-muted">{{ $t(`admin.dashboard.counts.${card.key}`) }}</p>
                    </Link>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <section class="rounded border border-line bg-surface p-4">
                        <h3 class="font-semibold text-ink">{{ $t('admin.dashboard.quick_links') }}</h3>
                        <ul class="mt-3 space-y-2 text-sm">
                            <li v-for="link in links" :key="link.route">
                                <Link :href="route(link.route)" class="text-primary-ink hover:underline">{{ $t(link.label) }}</Link>
                            </li>
                        </ul>
                    </section>
                    <section class="rounded border border-line bg-surface p-4 lg:col-span-2">
                        <h3 class="font-semibold text-ink">{{ $t('admin.dashboard.recent') }}</h3>
                        <AuditList class="mt-3" :logs="recent" />
                    </section>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
