<script setup>
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    type: { type: String, required: true },
    id: { type: Number, required: true },
});

const open = ref(false);
const form = useForm({ type: props.type, id: props.id, reason: '' });

const close = () => {
    open.value = false;
    form.reset('reason');
    form.clearErrors();
};

const submit = () => {
    form.post(route('reports.store'), { preserveScroll: true, onSuccess: close });
};
</script>

<template>
    <button type="button" class="text-xs text-gray-500 hover:text-red-700" @click="open = true">
        {{ $t('reports.report') }}
    </button>

    <Modal :show="open" max-width="md" @close="close">
        <form class="space-y-4 p-6" @submit.prevent="submit">
            <h3 class="text-lg font-semibold text-gray-900">{{ $t('reports.title') }}</h3>
            <div>
                <label :for="`report-${type}-${id}`" class="block text-sm font-medium text-gray-700">{{ $t('reports.reason') }}</label>
                <textarea
                    :id="`report-${type}-${id}`"
                    v-model="form.reason"
                    rows="3"
                    maxlength="1000"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                    :placeholder="$t('reports.reason_placeholder')"
                />
                <InputError class="mt-2" :message="form.errors.reason || form.errors.type || form.errors.id" />
            </div>
            <div class="flex justify-end gap-2">
                <SecondaryButton type="button" @click="close">{{ $t('common.cancel') }}</SecondaryButton>
                <PrimaryButton :disabled="form.processing">{{ $t('reports.submit') }}</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
