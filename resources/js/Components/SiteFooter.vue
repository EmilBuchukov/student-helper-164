<script setup>
const year = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-gray-200 bg-white">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
        >
            <p>{{ $t('footer.about') }}</p>
            <p>{{ $t('footer.rights', { year }) }}</p>
        </div>
    </footer>
</template>
