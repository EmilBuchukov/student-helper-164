<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    section: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="$t(section)" />

    <AppLayout>
        <div class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('soon.title') }}</h1>
            <p class="mt-3 text-gray-600">
                {{ $t('soon.text', { section: $t(section) }) }}
            </p>
            <Link
                :href="route('home')"
                class="mt-6 inline-block rounded-md bg-red-700 px-4 py-2 font-semibold text-white hover:bg-red-800"
            >
                {{ $t('common.back_home') }}
            </Link>
        </div>
    </AppLayout>
</template>
