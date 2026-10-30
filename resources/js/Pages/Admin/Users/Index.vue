<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    users: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="$t('admin.users.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('admin.users.title') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ $t('admin.users.intro') }}
                {{ $t('admin.users.total', { count: users.total }) }}
            </p>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
                <p v-if="users.data.length === 0" class="text-gray-600">
                    {{ $t('admin.users.empty') }}
                </p>

                <!-- Mobile: cards -->
                <ul class="space-y-3 md:hidden">
                    <li
                        v-for="user in users.data"
                        :key="user.id"
                        class="rounded-lg bg-white p-4 shadow-sm"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900">{{ user.name }}</p>
                                <p class="break-all text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'"
                            >
                                {{ user.is_active ? $t('admin.users.active') : $t('admin.users.inactive') }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-700">
                            {{ $t(`roles.${user.role}`) }} · {{ user.class_name ?? $t('common.none') }}
                        </p>
                    </li>
                </ul>

                <!-- Desktop: table -->
                <div class="hidden overflow-hidden rounded-lg bg-white shadow-sm md:block">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ $t('admin.users.name') }}</th>
                                <th class="px-4 py-3">{{ $t('admin.users.email') }}</th>
                                <th class="px-4 py-3">{{ $t('admin.users.role') }}</th>
                                <th class="px-4 py-3">{{ $t('admin.users.class') }}</th>
                                <th class="px-4 py-3">{{ $t('admin.users.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="user in users.data" :key="user.id">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ user.name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                                <td class="px-4 py-3">{{ $t(`roles.${user.role}`) }}</td>
                                <td class="px-4 py-3">{{ user.class_name ?? $t('common.none') }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'"
                                    >
                                        {{ user.is_active ? $t('admin.users.active') : $t('admin.users.inactive') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="users.links" />
            </div>
        </div>
    </AppLayout>
</template>
