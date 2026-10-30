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
    event: { type: Object, default: null },
    clubs: { type: Array, required: true },
    canSchoolWide: { type: Boolean, required: true },
    defaultClubId: { type: Number, default: null },
});

const editing = computed(() => props.event !== null);
const form = useForm({
    club_id: props.event ? (props.event.club_id ?? '') : (props.defaultClubId ?? (props.canSchoolWide ? '' : props.clubs[0]?.id ?? '')),
    title: props.event?.title ?? '',
    description: props.event?.description ?? '',
    starts_at: props.event?.starts_at ?? '',
    ends_at: props.event?.ends_at ?? '',
    location: props.event?.location ?? '',
});

const submit = () => {
    form.transform((data) => ({ ...data, club_id: data.club_id || null, ends_at: data.ends_at || null }));
    if (editing.value) {
        form.patch(route('events.update', props.event.id));
    } else {
        form.post(route('events.store'));
    }
};
</script>

<template>
    <Head :title="editing ? $t('events.edit_title') : $t('events.create_title')" />

    <AppLayout>
        <template #header>
            <Link :href="editing ? route('events.show', event.id) : route('events.index')" class="text-sm text-gray-500 hover:text-gray-800">← {{ $t('common.back') }}</Link>
            <h2 class="mt-2 text-xl font-semibold leading-tight text-gray-800">{{ editing ? $t('events.edit_title') : $t('events.create_title') }}</h2>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form class="space-y-5 rounded-lg bg-white p-4 shadow-sm sm:p-6" @submit.prevent="submit">
                    <div>
                        <InputLabel for="club" :value="$t('events.club')" />
                        <SelectInput id="club" v-model="form.club_id" class="mt-1 block w-full">
                            <option v-if="canSchoolWide" value="">{{ $t('events.school_wide') }}</option>
                            <option v-for="c in clubs" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.club_id" />
                    </div>
                    <div>
                        <InputLabel for="title" :value="$t('events.title_field')" />
                        <TextInput id="title" v-model="form.title" class="mt-1 block w-full" maxlength="200" required />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="starts_at" :value="$t('events.starts_at')" />
                            <TextInput id="starts_at" v-model="form.starts_at" type="datetime-local" class="mt-1 block w-full" required />
                            <InputError class="mt-2" :message="form.errors.starts_at" />
                        </div>
                        <div>
                            <InputLabel for="ends_at" :value="$t('events.ends_at')" />
                            <TextInput id="ends_at" v-model="form.ends_at" type="datetime-local" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.ends_at" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="location" :value="$t('events.location')" />
                        <TextInput id="location" v-model="form.location" class="mt-1 block w-full" maxlength="200" required />
                        <InputError class="mt-2" :message="form.errors.location" />
                    </div>
                    <div>
                        <InputLabel for="description" :value="$t('events.description')" />
                        <textarea id="description" v-model="form.description" rows="6" maxlength="5000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                    <PrimaryButton :disabled="form.processing">{{ editing ? $t('common.save') : $t('common.create') }}</PrimaryButton>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
