<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const success = computed(() => usePage().props.flash?.success);
</script>

<template>
    <div
        v-if="success"
        class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
        role="status"
    >
        {{ success }}
    </div>
</template>
