<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <Head :title="$t('dashboard.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('dashboard.title') }}
            </h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $t('dashboard.greeting', { name: user.name }) }}
                    </h3>
                    <p class="mt-1 text-gray-600">{{ $t('dashboard.intro') }}</p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">
                        {{ $t('dashboard.your_info') }}
                    </h3>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-md bg-gray-50 p-4">
                            <dt class="text-sm text-gray-500">{{ $t('dashboard.name') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900" data-test="user-name">
                                {{ user.name }}
                            </dd>
                        </div>
                        <div class="rounded-md bg-gray-50 p-4">
                            <dt class="text-sm text-gray-500">{{ $t('dashboard.class') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900" data-test="user-class">
                                {{ user.class_name ?? $t('dashboard.no_class') }}
                            </dd>
                        </div>
                        <div class="rounded-md bg-gray-50 p-4">
                            <dt class="text-sm text-gray-500">{{ $t('dashboard.role') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900" data-test="user-role">
                                {{ user.role ? $t(`roles.${user.role}`) : $t('common.none') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
