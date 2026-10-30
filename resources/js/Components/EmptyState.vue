<script setup>
defineProps({
    title: { type: String, required: true },
    hint: { type: String, default: '' },
});
</script>

<template>
    <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white px-6 py-10 text-center">
        <p class="font-medium text-gray-700">{{ title }}</p>
        <p v-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
