<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    report: {
        type: Object,
        default: null,
    },
    stats: {
        type: Object,
        required: true,
    },
});

const fileInput = ref(null);
const form = useForm({ file: null });

const submit = () => {
    form.post(route('admin.students.import.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const sections = [
    { key: 'created', label: 'admin.import.created', color: 'text-green-700' },
    { key: 'skipped', label: 'admin.import.skipped', color: 'text-yellow-700' },
    { key: 'invalid', label: 'admin.import.invalid', color: 'text-red-700' },
];
</script>

<template>
    <Head :title="$t('admin.import.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('admin.import.title') }}
            </h2>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <FlashMessage />

                <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <p class="text-sm text-gray-600">{{ $t('admin.import.intro') }}</p>
                    <p class="mt-2 text-xs text-gray-500">{{ $t('admin.import.sample') }}</p>

                    <form class="mt-4 space-y-4" @submit.prevent="submit">
                        <div>
                            <InputLabel for="file" :value="$t('admin.import.file')" />
                            <input
                                id="file"
                                ref="fileInput"
                                type="file"
                                accept=".csv,text/csv,text/plain"
                                class="mt-1 block w-full text-sm text-gray-700 file:me-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200"
                                @input="form.file = $event.target.files[0]"
                            />
                            <InputError class="mt-2" :message="form.errors.file" />
                        </div>
                        <PrimaryButton :disabled="form.processing || !form.file">
                            {{ $t('admin.import.submit') }}
                        </PrimaryButton>
                    </form>
                </div>

                <div class="rounded-lg bg-white p-4 text-sm text-gray-700 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-gray-900">{{ $t('admin.import.whitelist_title') }}</h3>
                    <p class="mt-1">
                        {{
                            $t('admin.import.whitelist_stats', {
                                total: stats.total,
                                claimed: stats.claimed,
                                pending: stats.total - stats.claimed,
                            })
                        }}
                    </p>
                </div>

                <div v-if="report" class="rounded-lg bg-white p-4 shadow-sm sm:p-6" data-test="import-report">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $t('admin.import.report') }}</h3>

                    <div class="mt-4 grid grid-cols-3 gap-3 text-center">
                        <div v-for="section in sections" :key="section.key" class="rounded-md bg-gray-50 p-3">
                            <div class="text-2xl font-bold" :class="section.color">
                                {{ report[section.key].length }}
                            </div>
                            <div class="text-xs text-gray-600">{{ $t(section.label) }}</div>
                        </div>
                    </div>

                    <template v-for="section in sections" :key="section.key">
                        <div v-if="report[section.key].length" class="mt-6">
                            <h4 class="font-medium" :class="section.color">{{ $t(section.label) }}</h4>
                            <ul class="mt-2 divide-y divide-gray-100 text-sm">
                                <li
                                    v-for="item in report[section.key]"
                                    :key="`${section.key}-${item.row}`"
                                    class="flex flex-col gap-0.5 py-2 sm:flex-row sm:gap-4"
                                >
                                    <span class="w-16 shrink-0 text-gray-500">
                                        {{ $t('admin.import.row') }} {{ item.row }}
                                    </span>
                                    <span class="break-all font-medium text-gray-900 sm:w-64">
                                        {{ item.email || $t('common.none') }}
                                    </span>
                                    <span v-if="item.reason" class="text-gray-600">{{ item.reason }}</span>
                                </li>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
