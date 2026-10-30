<script setup>
defineProps({
    profile: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ $t('profile.info_title') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">{{ $t('profile.info_intro') }}</p>
        </header>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-gray-500">{{ $t('profile.name') }}</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ profile.name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">{{ $t('profile.email') }}</dt>
                <dd class="mt-1 break-all font-medium text-gray-900">{{ profile.email }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">{{ $t('profile.class') }}</dt>
                <dd class="mt-1 font-medium text-gray-900">
                    {{ profile.class_name ?? $t('dashboard.no_class') }}
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">{{ $t('profile.role') }}</dt>
                <dd class="mt-1 font-medium text-gray-900">
                    {{ profile.role ? $t(`roles.${profile.role}`) : $t('common.none') }}
                </dd>
            </div>
        </dl>
    </section>
</template>
