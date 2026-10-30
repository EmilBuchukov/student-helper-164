<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import EmptyState from '@/Components/EmptyState.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { formatDateTime } from '@/format';
import { Head, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    reports: { type: Object, required: true },
    filters: { type: Object, required: true },
    statuses: { type: Array, required: true },
    types: { type: Array, required: true },
    muteDurations: { type: Array, required: true },
    canSuspend: { type: Boolean, required: true },
});

const filter = reactive({ ...props.filters });
const apply = () => router.get(route('moderation.reports.index'), Object.fromEntries(Object.entries(filter).filter(([, v]) => v)), { preserveState: true });

const active = ref(null);
const form = useForm({ remove_content: true, mute_minutes: '', suspend: false, note: '' });

const openResolve = (report) => {
    active.value = report;
    form.reset();
    form.clearErrors();
    form.remove_content = !report.content?.deleted;
};

const resolve = () => {
    form.transform((data) => ({ ...data, mute_minutes: data.mute_minutes || null }))
        .post(route('moderation.reports.resolve', active.value.id), { preserveScroll: true, onSuccess: () => (active.value = null) });
};

const dismiss = (report) => router.post(route('moderation.reports.dismiss', report.id), {}, { preserveScroll: true });
</script>

<template>
    <Head :title="$t('moderation.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('moderation.title') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $t('moderation.intro') }}</p>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm">
                    <SelectInput v-model="filter.status" class="block w-44" @change="apply">
                        <option v-for="s in statuses" :key="s" :value="s">{{ $t(`reports.statuses.${s}`) }}</option>
                        <option value="all">{{ $t('moderation.all_statuses') }}</option>
                    </SelectInput>
                    <SelectInput v-model="filter.type" class="block w-44" @change="apply">
                        <option value="">{{ $t('moderation.all_types') }}</option>
                        <option v-for="type in types" :key="type" :value="type">{{ $t(`moderation.types.${type}`) }}</option>
                    </SelectInput>
                </div>

                <EmptyState v-if="reports.data.length === 0" :title="$t('moderation.empty')" />

                <article v-for="report in reports.data" :key="report.id" class="rounded-lg bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded bg-gray-100 px-2 py-0.5 font-medium text-gray-700">{{ $t(`moderation.types.${report.type}`) }}</span>
                        <span
                            class="rounded-full px-2 py-0.5 font-medium"
                            :class="{ 'bg-red-100 text-red-800': report.status === 'open', 'bg-green-100 text-green-800': report.status === 'resolved', 'bg-gray-200 text-gray-700': report.status === 'dismissed' }"
                        >
                            {{ $t(`reports.statuses.${report.status}`) }}
                        </span>
                        <span class="text-gray-500">{{ $t('moderation.reported_by', { name: report.reporter ?? $t('common.deleted_user') }) }} · {{ formatDateTime(report.created_at) }}</span>
                    </div>
                    <p class="mt-2 break-words text-sm font-medium text-gray-900">„{{ report.reason }}“</p>

                    <div class="mt-3 rounded-md border border-gray-200 bg-gray-50 p-3 text-sm">
                        <template v-if="report.content">
                            <p v-if="report.content.title" class="break-words font-semibold text-gray-900">{{ report.content.title }}</p>
                            <p class="whitespace-pre-line break-words text-gray-700">{{ report.content.text }}</p>
                            <p class="mt-2 flex flex-wrap gap-x-3 text-xs text-gray-500">
                                <span>{{ $t('moderation.author', { name: report.content.author ?? $t('common.deleted_user') }) }}</span>
                                <span v-if="report.content.context">{{ $t('moderation.in_channel', { name: report.content.context }) }}</span>
                                <span v-if="report.content.deleted" class="font-semibold text-red-700">{{ $t('moderation.content_deleted') }}</span>
                                <a v-if="report.content.url && !report.content.deleted" :href="report.content.url" class="text-red-700 hover:underline">{{ $t('moderation.open_content') }}</a>
                            </p>
                        </template>
                        <p v-else class="text-gray-500">{{ $t('moderation.content_missing') }}</p>
                    </div>

                    <p v-if="report.handler" class="mt-2 text-xs text-gray-500">
                        {{ $t('moderation.handled_by', { name: report.handler, date: formatDateTime(report.handled_at) }) }}<template v-if="report.note"> — {{ report.note }}</template>
                    </p>

                    <div v-if="report.status === 'open'" class="mt-3 flex gap-2">
                        <PrimaryButton type="button" @click="openResolve(report)">{{ $t('moderation.resolve') }}</PrimaryButton>
                        <SecondaryButton type="button" @click="dismiss(report)">{{ $t('moderation.dismiss') }}</SecondaryButton>
                    </div>
                </article>

                <Pagination :links="reports.links" />
            </div>
        </div>

        <Modal :show="active !== null" max-width="md" @close="active = null">
            <form v-if="active" class="space-y-4 p-6" @submit.prevent="resolve">
                <h3 class="text-lg font-semibold text-gray-900">{{ $t('moderation.resolve_title') }}</h3>
                <label v-if="active.content && !active.content.deleted" class="flex items-center gap-2 text-sm text-gray-700">
                    <Checkbox v-model:checked="form.remove_content" />
                    {{ $t('moderation.remove_content') }}
                </label>
                <label v-if="active.content?.author_id && !active.content.author_protected" class="block text-sm text-gray-700">
                    {{ $t('moderation.mute_author') }}
                    <SelectInput v-model="form.mute_minutes" class="mt-1 block w-full">
                        <option value="">{{ $t('moderation.no_mute') }}</option>
                        <option v-for="d in muteDurations" :key="d" :value="d">{{ $t(`channels.durations.${d}`) }}</option>
                    </SelectInput>
                </label>
                <label v-if="canSuspend && active.content?.author_id && !active.content.author_protected" class="flex items-center gap-2 text-sm text-gray-700">
                    <Checkbox v-model:checked="form.suspend" />
                    {{ $t('moderation.suspend_author') }}
                </label>
                <label class="block text-sm text-gray-700">
                    {{ $t('moderation.note') }}
                    <textarea v-model="form.note" rows="2" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500" />
                </label>
                <InputError :message="form.errors.mute_minutes || form.errors.note" />
                <div class="flex justify-end gap-2">
                    <SecondaryButton type="button" @click="active = null">{{ $t('common.cancel') }}</SecondaryButton>
                    <PrimaryButton :disabled="form.processing">{{ $t('moderation.resolve') }}</PrimaryButton>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
