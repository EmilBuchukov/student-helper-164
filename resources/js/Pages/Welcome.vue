<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const features = [
    { title: 'nav.exams', text: 'welcome.feature_exams' },
    { title: 'nav.questions', text: 'welcome.feature_questions' },
    { title: 'nav.clubs', text: 'welcome.feature_clubs' },
];
</script>

<template>
    <Head :title="$t('welcome.title')" />

    <AppLayout>
        <section class="bg-gradient-to-b from-red-700 to-red-800 text-white">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-wide text-yellow-300">
                    {{ $t('app.full_name') }}
                </p>
                <h1 class="mt-3 text-3xl font-bold leading-tight sm:text-5xl">
                    {{ $t('welcome.heading') }}
                </h1>
                <p class="mt-4 max-w-2xl text-base text-red-50 sm:text-lg">
                    {{ $t('welcome.lead') }}
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="route('dashboard')"
                            class="rounded-md bg-yellow-300 px-5 py-3 text-center font-semibold text-red-900 hover:bg-yellow-200"
                        >
                            {{ $t('welcome.cta_dashboard') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-yellow-300 px-5 py-3 text-center font-semibold text-red-900 hover:bg-yellow-200"
                        >
                            {{ $t('welcome.cta_register') }}
                        </Link>
                        <Link
                            :href="route('login')"
                            class="rounded-md border border-white/60 px-5 py-3 text-center font-semibold text-white hover:bg-white/10"
                        >
                            {{ $t('welcome.cta_login') }}
                        </Link>
                    </template>
                </div>
                <p v-if="!$page.props.auth.user" class="mt-4 text-sm text-red-100">
                    {{ $t('welcome.whitelist_note') }}
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ $t('welcome.features_title') }}
            </h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="rounded-lg bg-white p-5 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">
                            {{ $t(feature.title) }}
                        </h3>
                        <span
                            class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                        >
                            {{ $t('common.coming_soon') }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">{{ $t(feature.text) }}</p>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
