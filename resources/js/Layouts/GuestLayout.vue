<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SiteFooter from '@/Components/SiteFooter.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-100">
        <div class="flex flex-1 flex-col items-center px-4 pt-8 sm:justify-center sm:pt-0">
            <Link :href="route('home')">
                <ApplicationLogo />
            </Link>

            <div
                class="mt-6 w-full overflow-hidden rounded-lg bg-white px-6 py-6 shadow-md sm:max-w-md"
            >
                <h1 v-if="title" class="mb-4 text-xl font-semibold text-gray-900">
                    {{ title }}
                </h1>
                <slot />
            </div>
        </div>
        <SiteFooter class="mt-8" />
    </div>
</template>
