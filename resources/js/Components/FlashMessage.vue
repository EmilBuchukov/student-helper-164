<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const success = computed(() => usePage().props.flash?.success);
const error = computed(() => usePage().props.flash?.error);
</script>

<template>
    <div
        v-if="success"
        class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
        role="status"
    >
        {{ success }}
    </div>
    <div
        v-if="error"
        class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
        role="alert"
    >
        {{ error }}
    </div>
</template>
