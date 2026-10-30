<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    url: { type: String, required: true },
    count: { type: Number, required: true },
    voted: { type: Boolean, default: false },
    canVote: { type: Boolean, default: false },
});

const busy = ref(false);

const toggle = () => {
    if (!props.canVote || busy.value) return;
    router.post(props.url, {}, {
        preserveScroll: true,
        onStart: () => (busy.value = true),
        onFinish: () => (busy.value = false),
    });
};
</script>

<template>
    <button
        type="button"
        class="flex min-w-[3rem] flex-col items-center rounded-md border px-2 py-1 text-sm transition disabled:cursor-default"
        :class="voted ? 'border-red-300 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
        :disabled="!canVote || busy"
        :title="canVote ? (voted ? $t('votes.remove') : $t('votes.upvote')) : $t('votes.own')"
        :aria-pressed="voted"
        @click="toggle"
    >
        <span aria-hidden="true">▲</span>
        <span class="font-semibold">{{ count }}</span>
    </button>
</template>
