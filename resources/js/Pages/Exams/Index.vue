<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { formatDate } from '@/format';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const props = defineProps({
    exams: { type: Object, required: true },
    filters: { type: Object, required: true },
    subjects: { type: Array, required: true },
    scopeOptions: { type: Array, required: true },
    can: { type: Object, required: true },
});

const form = reactive({ ...props.filters });
const loading = ref(false);

const apply = () => {
    const query = Object.fromEntries(Object.entries(form).filter(([, v]) => v));
    router.get(route('exams.index'), query, {
        preserveState: true,
        preserveScroll: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    });
};

const reset = () => {
    Object.keys(form).forEach((key) => (form[key] = ''));
    apply();
};
</script>

<template>
    <Head :title="$t('exams.title')" />

    <AppLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('exams.title') }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $t('exams.intro') }}</p>
                </div>
                <Link
                    v-if="can.create"
                    :href="route('exams.create')"
                    class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800"
                >
                    {{ $t('exams.new') }}
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <form
                    class="grid gap-3 rounded-lg bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5"
                    @submit.prevent="apply"
                >
                    <label class="text-sm text-gray-700">
                        {{ $t('exams.subject') }}
                        <SelectInput v-model="form.subject" class="mt-1 block w-full">
                            <option value="">{{ $t('exams.all_subjects') }}</option>
                            <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
                        </SelectInput>
                    </label>
                    <label class="text-sm text-gray-700">
                        {{ $t('exams.from') }}
                        <TextInput v-model="form.from" type="date" class="mt-1 block w-full text-sm" />
                    </label>
                    <label class="text-sm text-gray-700">
                        {{ $t('exams.to') }}
                        <TextInput v-model="form.to" type="date" class="mt-1 block w-full text-sm" />
                    </label>
                    <label class="text-sm text-gray-700">
                        {{ $t('scope.label') }}
                        <SelectInput v-model="form.scope" class="mt-1 block w-full">
                            <option value="">{{ $t('exams.all_scopes') }}</option>
                            <option v-for="o in scopeOptions" :key="o.value" :value="o.value">{{ o.label }}</option>
                        </SelectInput>
                    </label>
                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="flex-1 rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700 disabled:opacity-50"
                        >
                            {{ loading ? $t('common.loading') : $t('common.filter') }}
                        </button>
                        <button
                            type="button"
                            class="rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-100"
                            @click="reset"
                        >
                            {{ $t('common.reset') }}
                        </button>
                    </div>
                </form>

                <EmptyState
                    v-if="exams.data.length === 0"
                    :title="$t('exams.empty')"
                    :hint="can.create ? $t('exams.empty_hint') : ''"
                />

                <ul v-else class="space-y-3" :class="{ 'opacity-60': loading }">
                    <li v-for="exam in exams.data" :key="exam.id">
                        <Link
                            :href="route('exams.show', exam.id)"
                            class="block rounded-lg bg-white p-4 shadow-sm transition hover:shadow"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase text-red-700">{{ exam.subject }}</p>
                                    <p class="mt-0.5 font-semibold text-gray-900">{{ exam.title }}</p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $t(`exams.types.${exam.type}`) }} · {{ exam.audience }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">{{ formatDate(exam.exam_date) }}</p>
                                    <span
                                        class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="exam.is_upcoming ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'"
                                    >
                                        {{ exam.is_upcoming ? $t('exams.upcoming') : $t('exams.past') }}
                                    </span>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                {{ $t('exams.materials_count', { count: exam.materials_count }) }}
                            </p>
                        </Link>
                    </li>
                </ul>

                <Pagination :links="exams.links" />
            </div>
        </div>
    </AppLayout>
</template>
