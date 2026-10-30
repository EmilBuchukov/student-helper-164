<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    club: { type: Object, default: null },
    leaders: { type: Array, required: true },
});

const editing = computed(() => props.club !== null);
const form = useForm({
    name: props.club?.name ?? '',
    description: props.club?.description ?? '',
    leader_id: props.club?.leader_id ?? '',
    is_active: props.club?.is_active ?? true,
});

const submit = () => {
    const options = { transform: (data) => ({ ...data, leader_id: data.leader_id || null }) };
    if (editing.value) {
        form.transform(options.transform).patch(route('clubs.update', props.club.slug));
    } else {
        form.transform(options.transform).post(route('clubs.store'));
    }
};
</script>

<template>
    <Head :title="editing ? $t('clubs.edit_title') : $t('clubs.create_title')" />

    <AppLayout>
        <template #header>
            <Link :href="editing ? route('clubs.show', club.slug) : route('clubs.index')" class="text-sm text-gray-500 hover:text-gray-800">← {{ $t('common.back') }}</Link>
            <h2 class="mt-2 text-xl font-semibold leading-tight text-gray-800">{{ editing ? $t('clubs.edit_title') : $t('clubs.create_title') }}</h2>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form class="space-y-5 rounded-lg bg-white p-4 shadow-sm sm:p-6" @submit.prevent="submit">
                    <div>
                        <InputLabel for="name" :value="$t('clubs.name')" />
                        <TextInput id="name" v-model="form.name" class="mt-1 block w-full" maxlength="120" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="description" :value="$t('clubs.description')" />
                        <textarea id="description" v-model="form.description" rows="6" maxlength="5000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                    <div>
                        <InputLabel for="leader" :value="$t('clubs.leader')" />
                        <SelectInput id="leader" v-model="form.leader_id" class="mt-1 block w-full">
                            <option value="">{{ $t('clubs.no_leader') }}</option>
                            <option v-for="l in leaders" :key="l.id" :value="l.id">{{ l.label }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.leader_id" />
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <Checkbox v-model:checked="form.is_active" />
                        {{ $t('clubs.is_active') }}
                    </label>
                    <PrimaryButton :disabled="form.processing">{{ editing ? $t('common.save') : $t('common.create') }}</PrimaryButton>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
