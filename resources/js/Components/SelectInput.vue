<script setup>
const model = defineModel({ type: [String, Number], default: '' });
</script>

<template>
    <select
        v-model="model"
        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
    >
        <slot />
    </select>
</template>
