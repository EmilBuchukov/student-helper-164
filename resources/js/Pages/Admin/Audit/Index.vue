<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AuditList from '@/Pages/Admin/Audit/AuditList.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    logs: { type: Object, required: true },
    filters: { type: Object, required: true },
    actions: { type: Array, required: true },
});

const action = ref(props.filters.action);
const apply = () => router.get(route('admin.audit.index'), action.value ? { action: action.value } : {}, { preserveState: true });
</script>

<template>
    <Head :title="$t('admin.audit.title')" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $t('admin.audit.title') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $t('admin.audit.intro') }}</p>
        </template>

        <div class="py-6 sm:py-10">
            <div class="mx-auto max-w-5xl space-y-4 px-4 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <label class="block max-w-xs text-sm text-gray-700">
                        {{ $t('admin.audit.action') }}
                        <SelectInput v-model="action" class="mt-1 block w-full" @change="apply">
                            <option value="">{{ $t('admin.audit.all_actions') }}</option>
                            <option v-for="a in actions" :key="a" :value="a">{{ $t(`admin.audit.actions.${a}`) }}</option>
                        </SelectInput>
                    </label>
                </div>
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <AuditList :logs="logs.data" />
                </div>
                <Pagination :links="logs.links" />
            </div>
        </div>
    </AppLayout>
</template>
