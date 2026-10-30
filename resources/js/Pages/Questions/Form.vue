<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    question: { type: Object, default: null },
    scopeOptions: { type: Array, required: true },
    subjects: { type: Array, required: true },
});

const editing = computed(() => props.question !== null);
const form = useForm({
    title: props.question?.title ?? '',
    body: props.question?.body ?? '',
    subject: props.question?.subject ?? '',
    scope: props.question?.scope ?? '',
});

const submit = () => {
    if (editing.value) {
        form.patch(route('questions.update', props.question.id));
    } else {
        form.post(route('questions.store'));
    }
};
</script>

<template>
    <Head :title="editing ? $t('questions.edit_title') : $t('questions.create_title')" />

    <AppLayout>
        <template #header>
            <Link :href="editing ? route('questions.show', question.id) : route('questions.index')" class="text-sm text-gray-500 hover:text-gray-800">
                ← {{ $t('common.back') }}
            </Link>
            <h2 class="mt-2 text-xl font-semibold leading-tight text-gray-800">
                {{ editing ? $t('questions.edit_title') : $t('questions.create_title') }}
            </h2>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form class="space-y-5 rounded-lg bg-white p-4 shadow-sm sm:p-6" @submit.prevent="submit">
                    <div>
                        <InputLabel for="title" :value="$t('questions.title_field')" />
                        <TextInput id="title" v-model="form.title" class="mt-1 block w-full" maxlength="200" required :placeholder="$t('questions.title_placeholder')" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>
                    <div>
                        <InputLabel for="body" :value="$t('questions.body')" />
                        <textarea
                            id="body"
                            v-model="form.body"
                            rows="8"
                            maxlength="10000"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            :placeholder="$t('questions.body_placeholder')"
                        />
                        <InputError class="mt-2" :message="form.errors.body" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="subject" :value="$t('questions.subject_optional')" />
                            <TextInput id="subject" v-model="form.subject" list="subject-suggestions" class="mt-1 block w-full" maxlength="100" />
                            <datalist id="subject-suggestions">
                                <option v-for="s in subjects" :key="s" :value="s" />
                            </datalist>
                            <InputError class="mt-2" :message="form.errors.subject" />
                        </div>
                        <div>
                            <InputLabel for="scope" :value="$t('scope.label')" />
                            <SelectInput id="scope" v-model="form.scope" class="mt-1 block w-full">
                                <option value="">{{ $t('scope.school_wide') }}</option>
                                <option v-for="o in scopeOptions" :key="o.value" :value="o.value">{{ o.label }}</option>
                            </SelectInput>
                            <p class="mt-1 text-xs text-gray-500">{{ $t('questions.scope_hint') }}</p>
                            <InputError class="mt-2" :message="form.errors.scope" />
                        </div>
                    </div>
                    <PrimaryButton :disabled="form.processing">
                        {{ editing ? $t('common.save') : $t('questions.publish') }}
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
