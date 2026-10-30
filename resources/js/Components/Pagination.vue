<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex flex-wrap gap-1">
        <template v-for="(link, index) in links" :key="index">
            <span
                v-if="!link.url"
                class="rounded-md px-3 py-1 text-sm text-gray-400"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded-md px-3 py-1 text-sm"
                :class="link.active ? 'bg-red-700 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
