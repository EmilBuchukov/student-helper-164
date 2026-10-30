<script setup>
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import SiteFooter from '@/Components/SiteFooter.vue';
import { adminNav, mainNav } from '@/navigation';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const showingMenu = ref(false);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-100">
        <nav class="sticky top-0 z-40 border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('home')">
                                <ApplicationLogo />
                            </Link>
                        </div>

                        <div class="hidden space-x-6 md:-my-px md:ms-10 md:flex">
                            <NavLink
                                v-for="item in mainNav"
                                :key="item.route"
                                :href="route(item.route)"
                                :active="route().current(item.active ?? item.route)"
                            >
                                {{ $t(item.label) }}
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden md:ms-6 md:flex md:items-center">
                        <div v-if="user" class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition hover:text-gray-800 focus:outline-none"
                                        >
                                            {{ user.name }}
                                            <svg
                                                class="-me-0.5 ms-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('dashboard')">
                                        {{ $t('nav.dashboard') }}
                                    </DropdownLink>
                                    <DropdownLink :href="route('profile.edit')">
                                        {{ $t('nav.profile') }}
                                    </DropdownLink>
                                    <template v-if="user.is_admin">
                                        <div
                                            class="border-t border-gray-100 px-4 pt-2 text-xs font-semibold uppercase text-gray-400"
                                        >
                                            {{ $t('nav.admin') }}
                                        </div>
                                        <DropdownLink
                                            v-for="item in adminNav"
                                            :key="item.route"
                                            :href="route(item.route)"
                                        >
                                            {{ $t(item.label) }}
                                        </DropdownLink>
                                    </template>
                                    <div class="border-t border-gray-100" />
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        {{ $t('nav.logout') }}
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                        <div v-else class="flex items-center gap-3">
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                {{ $t('nav.login') }}
                            </Link>
                            <Link
                                :href="route('register')"
                                class="rounded-md bg-red-700 px-3 py-2 text-sm font-semibold text-white hover:bg-red-800"
                            >
                                {{ $t('nav.register') }}
                            </Link>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center md:hidden">
                        <button
                            type="button"
                            :aria-label="
                                showingMenu ? $t('nav.close_menu') : $t('nav.open_menu')
                            "
                            :aria-expanded="showingMenu"
                            @click="showingMenu = !showingMenu"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:bg-gray-100 focus:outline-none"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    v-if="!showingMenu"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    v-else
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-show="showingMenu" class="md:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink
                        v-for="item in mainNav"
                        :key="item.route"
                        :href="route(item.route)"
                        :active="route().current(item.active ?? item.route)"
                    >
                        {{ $t(item.label) }}
                    </ResponsiveNavLink>
                </div>

                <div v-if="user" class="border-t border-gray-200 pb-2 pt-4">
                    <div class="px-4">
                        <div class="text-base font-medium text-gray-800">
                            {{ user.name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">
                            {{ user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            {{ $t('nav.dashboard') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('profile.edit')"
                            :active="route().current('profile.edit')"
                        >
                            {{ $t('nav.profile') }}
                        </ResponsiveNavLink>
                        <template v-if="user.is_admin">
                            <ResponsiveNavLink
                                v-for="item in adminNav"
                                :key="item.route"
                                :href="route(item.route)"
                                :active="route().current(item.active ?? item.route)"
                            >
                                {{ $t(item.label) }}
                            </ResponsiveNavLink>
                        </template>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            {{ $t('nav.logout') }}
                        </ResponsiveNavLink>
                    </div>
                </div>
                <div v-else class="space-y-1 border-t border-gray-200 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('login')">
                        {{ $t('nav.login') }}
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('register')">
                        {{ $t('nav.register') }}
                    </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="flex-1">
            <div
                v-if="$page.props.flash?.success || $page.props.flash?.error"
                class="mx-auto max-w-7xl space-y-2 px-4 pt-4 sm:px-6 lg:px-8"
            >
                <FlashMessage />
            </div>
            <slot />
        </main>

        <SiteFooter />
    </div>
</template>
