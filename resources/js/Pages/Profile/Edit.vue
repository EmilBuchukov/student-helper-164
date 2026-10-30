<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ProfileInformation from './Partials/ProfileInformation.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    profile: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="$t('profile.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('profile.title') }}
            </h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <ProfileInformation :profile="profile" class="max-w-xl" />
                </div>

                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
