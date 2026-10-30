<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import Pagination from '@/Components/Pagination.vue';
import { formatDateTime } from '@/format';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    items: { type: Object, required: true },
    type: { type: String, required: true },
    types: { type: Array, required: true },
});

const restore = (item) => router.post(route('admin.deleted.restore', { type: props.type, id: item.id }), {}, { preserveScroll: true });
</script>

<template>
    <Head :title="$t('admin.deleted.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('admin.deleted.title') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $t('admin.deleted.intro') }}</p>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <nav class="flex gap-2">
                    <Link
                        v-for="item in types"
                        :key="item"
                        :href="route('admin.deleted.index', { type: item })"
                        class="rounded-full px-4 py-1.5 text-sm"
                        :class="item === type ? 'bg-gray-800 text-white' : 'bg-white text-gray-700 shadow-sm hover:bg-gray-50'"
                    >
                        {{ $t(`admin.deleted.types.${item}`) }}
                    </Link>
                </nav>

                <EmptyState v-if="items.data.length === 0" :title="$t('admin.deleted.empty')" />
                <ul v-else class="divide-y divide-gray-100 rounded-lg bg-white shadow-sm">
                    <li v-for="item in items.data" :key="item.id" class="flex flex-wrap items-start gap-3 px-4 py-3 text-sm">
                        <div class="min-w-0 flex-1">
                            <p class="break-words text-gray-900">{{ item.text }}</p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ item.author ?? $t('common.deleted_user') }}
                                <template v-if="item.context"> · {{ item.context }}</template>
                                · {{ $t('admin.deleted.deleted_at', { date: formatDateTime(item.deleted_at) }) }}
                            </p>
                        </div>
                        <button type="button" class="rounded-md bg-green-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-800" @click="restore(item)">
                            {{ $t('admin.deleted.restore') }}
                        </button>
                    </li>
                </ul>

                <Pagination :links="items.links" />
            </div>
        </div>
    </AppLayout>
</template>
