<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { formatDateTime } from '@/format';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    questions: { type: Object, required: true },
    filters: { type: Object, required: true },
    subjects: { type: Array, required: true },
    scopeOptions: { type: Array, required: true },
    can: { type: Object, required: true },
});

const sorts = ['newest', 'votes', 'unresolved'];
const form = reactive({ ...props.filters });
const loading = ref(false);

const apply = () => {
    const query = Object.fromEntries(Object.entries(form).filter(([k, v]) => v && !(k === 'sort' && v === 'newest')));
    router.get(route('questions.index'), query, {
        preserveState: true,
        preserveScroll: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    });
};

const setSort = (sort) => {
    form.sort = sort;
    apply();
};

const reset = () => {
    Object.assign(form, { q: '', subject: '', scope: '', sort: 'newest' });
    apply();
};
</script>

<template>
    <Head :title="$t('questions.title')" />

    <AppLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-display text-title-1 font-semibold text-ink">{{ $t('questions.title') }}</h1>
                    <p class="mt-1 text-sm text-ink-muted">{{ $t('questions.intro') }}</p>
                </div>
                <Link
                    v-if="can.create"
                    :href="route('questions.create')"
                    class="rounded bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary-hover"
                >
                    {{ $t('questions.new') }}
                </Link>
            </div>
        </template>

        <div>
            <div class="mx-auto max-w-5xl space-y-4">
                <form class="grid gap-3 rounded border border-line bg-surface p-4 sm:grid-cols-2 lg:grid-cols-4" @submit.prevent="apply">
                    <label class="text-sm text-ink-muted sm:col-span-2">
                        {{ $t('questions.search') }}
                        <TextInput v-model="form.q" type="search" maxlength="100" class="mt-1 block w-full text-sm" :placeholder="$t('questions.search_placeholder')" />
                    </label>
                    <label class="text-sm text-ink-muted">
                        {{ $t('questions.subject') }}
                        <SelectInput v-model="form.subject" class="mt-1 block w-full">
                            <option value="">{{ $t('questions.all_subjects') }}</option>
                            <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                        </SelectInput>
                    </label>
                    <label class="text-sm text-ink-muted">
                        {{ $t('scope.label') }}
                        <SelectInput v-model="form.scope" class="mt-1 block w-full">
                            <option value="">{{ $t('questions.all_scopes') }}</option>
                            <option value="school">{{ $t('scope.school_wide') }}</option>
                            <option v-for="o in scopeOptions" :key="o.value" :value="o.value">{{ o.label }}</option>
                        </SelectInput>
                    </label>
                    <div class="flex flex-wrap items-center gap-2 sm:col-span-2 lg:col-span-4">
                        <span class="text-sm text-ink-muted">{{ $t('questions.sort') }}:</span>
                        <button
                            v-for="s in sorts"
                            :key="s"
                            type="button"
                            class="rounded-full px-3 py-1 text-sm"
                            :class="form.sort === s ? 'bg-ink text-canvas' : 'bg-sunken text-ink-muted hover:bg-sunken'"
                            @click="setSort(s)"
                        >
                            {{ $t(`questions.sorts.${s}`) }}
                        </button>
                        <div class="ms-auto flex gap-2">
                            <button type="submit" :disabled="loading" class="rounded bg-ink px-3 py-2 text-sm font-semibold text-canvas hover:bg-ink/85 disabled:opacity-50">
                                {{ loading ? $t('common.loading') : $t('common.filter') }}
                            </button>
                            <button type="button" class="rounded px-3 py-2 text-sm text-ink-muted hover:bg-sunken" @click="reset">
                                {{ $t('common.reset') }}
                            </button>
                        </div>
                    </div>
                </form>

                <EmptyState v-if="questions.data.length === 0" :title="$t('questions.empty')" :hint="can.create ? $t('questions.empty_hint') : ''" />

                <ul v-else class="space-y-3" :class="{ 'opacity-60': loading }">
                    <li v-for="question in questions.data" :key="question.id">
                        <Link :href="route('questions.show', question.id)" class="flex gap-4 rounded border border-line bg-surface p-4 transition">
                            <div class="hidden w-16 shrink-0 flex-col items-center gap-1 text-xs text-ink-subtle sm:flex">
                                <span class="text-lg font-semibold text-ink">{{ question.votes_count }}</span>
                                <span>▲</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="question.resolved ? 'bg-success-soft text-success' : 'bg-accent-soft text-accent-ink'"
                                    >
                                        {{ question.resolved ? $t('questions.resolved') : $t('questions.unresolved') }}
                                    </span>
                                    <span v-if="question.subject" class="text-xs font-semibold uppercase tracking-[0.1em] text-primary-ink">{{ question.subject }}</span>
                                    <span class="text-xs text-ink-subtle">{{ question.audience }}</span>
                                </div>
                                <p class="mt-1 break-words font-semibold text-ink">{{ question.title }}</p>
                                <p class="mt-1 break-words text-sm text-ink-muted">{{ question.excerpt }}</p>
                                <p class="mt-2 text-xs text-ink-subtle">
                                    {{ question.author ?? $t('common.deleted_user') }} · {{ formatDateTime(question.created_at) }} ·
                                    {{ $t('questions.answers_count', { count: question.answers_count }) }}
                                    <span class="sm:hidden"> · {{ $t('questions.votes_count', { count: question.votes_count }) }}</span>
                                </p>
                            </div>
                        </Link>
                    </li>
                </ul>

                <Pagination :links="questions.links" />
            </div>
        </div>
    </AppLayout>
</template>
