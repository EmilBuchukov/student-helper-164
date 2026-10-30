<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { formatDate, formatDateTime, formatSize } from '@/format';
import { t } from '@/i18n';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    exam: { type: Object, required: true },
    materials: { type: Array, required: true },
    upload: { type: Object, required: true },
});

const fileInput = ref(null);
const form = useForm({ title: '', file: null });

const submit = () => {
    form.post(route('exams.materials.store', props.exam.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const destroyExam = () => {
    if (confirm(t('common.confirm_delete'))) {
        router.delete(route('exams.destroy', props.exam.id));
    }
};

const destroyMaterial = (material) => {
    if (confirm(t('common.confirm_delete'))) {
        router.delete(route('materials.destroy', material.id), { preserveScroll: true });
    }
};

const accept = props.upload.extensions.map((e) => `.${e}`).join(',');
</script>

<template>
    <Head :title="exam.title" />

    <AppLayout>
        <template #header>
            <Link :href="route('exams.index')" class="text-sm text-gray-500 hover:text-gray-800">
                ← {{ $t('exams.title') }}
            </Link>
            <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase text-red-700">{{ exam.subject }}</p>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ exam.title }}</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ formatDate(exam.exam_date) }} · {{ $t(`exams.types.${exam.type}`) }} · {{ exam.audience }}
                    </p>
                    <p v-if="exam.creator" class="mt-1 text-xs text-gray-500">
                        {{ $t('exams.created_by', { name: exam.creator }) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="exam.can.update"
                        :href="route('exams.edit', exam.id)"
                        class="rounded-md border border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        {{ $t('common.edit') }}
                    </Link>
                    <button
                        v-if="exam.can.delete"
                        type="button"
                        class="rounded-md border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50"
                        @click="destroyExam"
                    >
                        {{ $t('common.delete') }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div v-if="exam.description" class="whitespace-pre-line rounded-lg bg-white p-4 text-gray-700 shadow-sm sm:p-6">{{ exam.description }}</div>

                <section class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $t('materials.title') }}</h3>

                    <EmptyState v-if="materials.length === 0" :title="$t('materials.empty')" />

                    <ul v-else class="divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                        <li
                            v-for="material in materials"
                            :key="material.id"
                            class="flex flex-wrap items-center justify-between gap-2 p-4"
                        >
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900">{{ material.title }}</p>
                                <p class="break-all text-xs text-gray-500">
                                    {{ material.original_name }} · {{ formatSize(material.size) }} ·
                                    {{ material.author ?? $t('common.deleted_user') }} · {{ formatDateTime(material.created_at) }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a
                                    :href="material.download_url"
                                    class="rounded-md bg-gray-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-700"
                                >
                                    {{ $t('materials.download') }}
                                </a>
                                <button
                                    v-if="material.can_delete"
                                    type="button"
                                    class="rounded-md px-3 py-1.5 text-sm text-red-700 hover:bg-red-50"
                                    @click="destroyMaterial(material)"
                                >
                                    {{ $t('common.delete') }}
                                </button>
                            </div>
                        </li>
                    </ul>
                </section>

                <section v-if="exam.can.upload" class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-gray-900">{{ $t('materials.upload_title') }}</h3>
                    <form class="mt-3 space-y-4" @submit.prevent="submit">
                        <div>
                            <InputLabel for="material-title" :value="$t('materials.title_field')" />
                            <TextInput id="material-title" v-model="form.title" class="mt-1 block w-full" maxlength="255" required />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div>
                            <InputLabel for="material-file" :value="$t('materials.file')" />
                            <input
                                id="material-file"
                                ref="fileInput"
                                type="file"
                                :accept="accept"
                                class="mt-1 block w-full text-sm text-gray-700 file:me-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200"
                                @input="form.file = $event.target.files[0]"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $t('materials.allowed', { types: upload.extensions.join(', '), max: upload.max_mb }) }}
                            </p>
                            <InputError class="mt-2" :message="form.errors.file" />
                        </div>
                        <div v-if="form.progress" class="h-2 overflow-hidden rounded bg-gray-100">
                            <div class="h-2 bg-red-600" :style="{ width: `${form.progress.percentage}%` }" />
                        </div>
                        <PrimaryButton :disabled="form.processing || !form.file">
                            {{ form.processing ? $t('materials.uploading') : $t('materials.upload') }}
                        </PrimaryButton>
                    </form>
                </section>
                <p v-else-if="$page.props.auth.user.role === 'teacher'" class="text-sm text-gray-500">
                    {{ $t('materials.teacher_note') }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
