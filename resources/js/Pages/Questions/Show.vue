<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ReportButton from '@/Components/ReportButton.vue';
import VoteButton from '@/Components/VoteButton.vue';
import { formatDateTime } from '@/format';
import { t } from '@/i18n';
import { confirmDialog } from '@/Components/ui/confirm';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    question: { type: Object, required: true },
    answers: { type: Array, required: true },
});

const answerForm = useForm({ body: '' });
const editingId = ref(null);
const editForm = useForm({ body: '' });

const submitAnswer = () => {
    answerForm.post(route('questions.answers.store', props.question.id), {
        preserveScroll: true,
        onSuccess: () => answerForm.reset(),
    });
};

const startEdit = (answer) => {
    editingId.value = answer.id;
    editForm.body = answer.body;
    editForm.clearErrors();
};

const saveEdit = (answer) => {
    editForm.patch(route('answers.update', answer.id), {
        preserveScroll: true,
        onSuccess: () => (editingId.value = null),
    });
};

const destroy = async (url) => {
    if (await confirmDialog(t('common.confirm_delete'))) {
        router.delete(url, { preserveScroll: true });
    }
};

const accept = (answer) => router.post(route('answers.accept', answer.id), {}, { preserveScroll: true });
</script>

<template>
    <Head :title="question.title" />

    <AppLayout>
        <template #header>
            <Link :href="route('questions.index')" class="text-sm text-ink-subtle hover:text-ink">← {{ $t('questions.title') }}</Link>
        </template>

        <div>
            <div class="mx-auto max-w-4xl space-y-6">
                <article class="flex gap-3 rounded border border-line bg-surface p-4 sm:gap-4 sm:p-6">
                    <VoteButton
                        :url="route('questions.vote', question.id)"
                        :count="question.votes_count"
                        :voted="question.voted"
                        :can-vote="question.can.vote"
                    />
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="question.resolved ? 'bg-success-soft text-success' : 'bg-accent-soft text-accent-ink'"
                            >
                                {{ question.resolved ? $t('questions.resolved') : $t('questions.unresolved') }}
                            </span>
                            <span v-if="question.subject" class="text-xs font-semibold uppercase tracking-[0.1em] text-primary-ink">{{ question.subject }}</span>
                            <span class="text-xs text-ink-subtle">{{ question.audience }}</span>
                        </div>
                        <h1 class="mt-2 break-words text-xl font-semibold text-ink">{{ question.title }}</h1>
                        <div class="mt-3 whitespace-pre-line break-words text-ink-muted">{{ question.body }}</div>
                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-ink-subtle">
                            <span>
                                {{ $t('questions.asked_by', { name: question.author ?? $t('common.deleted_user') }) }} ·
                                {{ formatDateTime(question.created_at) }}<template v-if="question.edited"> · {{ $t('questions.edited') }}</template>
                            </span>
                            <Link v-if="question.can.update" :href="route('questions.edit', question.id)" class="hover:text-ink">{{ $t('common.edit') }}</Link>
                            <button v-if="question.can.delete" type="button" class="text-primary-ink hover:underline" @click="destroy(route('questions.destroy', question.id))">
                                {{ $t('common.delete') }}
                            </button>
                            <ReportButton v-if="question.can.report" type="question" :id="question.id" />
                        </div>
                    </div>
                </article>

                <section class="space-y-3">
                    <h2 class="text-lg font-semibold text-ink">{{ $t('answers.title') }} ({{ answers.length }})</h2>

                    <EmptyState v-if="answers.length === 0" :title="$t('answers.empty')" />

                    <article
                        v-for="answer in answers"
                        :key="answer.id"
                        class="flex gap-3 rounded border border-line bg-surface p-4 sm:gap-4"
                        :class="{ 'ring-2 ring-green-500': answer.accepted }"
                    >
                        <VoteButton :url="route('answers.vote', answer.id)" :count="answer.votes_count" :voted="answer.voted" :can-vote="answer.can.vote" />
                        <div class="min-w-0 flex-1">
                            <p v-if="answer.accepted" class="mb-2 text-xs font-semibold text-success">✓ {{ $t('answers.accepted_badge') }}</p>

                            <form v-if="editingId === answer.id" class="space-y-2" @submit.prevent="saveEdit(answer)">
                                <textarea v-model="editForm.body" rows="4" maxlength="10000" required class="block w-full rounded border-line-strong text-sm focus:border-primary-ink focus:ring-focus/30" />
                                <InputError :message="editForm.errors.body" />
                                <div class="flex gap-2">
                                    <PrimaryButton :disabled="editForm.processing">{{ $t('common.save') }}</PrimaryButton>
                                    <button type="button" class="text-sm text-ink-muted" @click="editingId = null">{{ $t('common.cancel') }}</button>
                                </div>
                            </form>
                            <div v-else class="whitespace-pre-line break-words text-ink-muted">{{ answer.body }}</div>

                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-ink-subtle">
                                <span>
                                    {{ answer.author ?? $t('common.deleted_user') }} · {{ formatDateTime(answer.created_at) }}<template v-if="answer.edited"> · {{ $t('questions.edited') }}</template>
                                </span>
                                <button v-if="question.can.accept" type="button" class="font-medium text-success hover:underline" @click="accept(answer)">
                                    {{ answer.accepted ? $t('answers.unaccept') : $t('answers.accept') }}
                                </button>
                                <button v-if="answer.can.update && editingId !== answer.id" type="button" class="hover:text-ink" @click="startEdit(answer)">{{ $t('common.edit') }}</button>
                                <button v-if="answer.can.delete" type="button" class="text-primary-ink hover:underline" @click="destroy(route('answers.destroy', answer.id))">
                                    {{ $t('common.delete') }}
                                </button>
                                <ReportButton v-if="answer.can.report" type="answer" :id="answer.id" />
                            </div>
                        </div>
                    </article>
                </section>

                <form v-if="question.can.answer" class="space-y-3 rounded border border-line bg-surface p-4 sm:p-6" @submit.prevent="submitAnswer">
                    <label for="answer-body" class="block font-semibold text-ink">{{ $t('answers.body') }}</label>
                    <textarea
                        id="answer-body"
                        v-model="answerForm.body"
                        rows="5"
                        maxlength="10000"
                        required
                        class="block w-full rounded border-line-strong focus:border-primary-ink focus:ring-focus/30"
                        :placeholder="$t('answers.placeholder')"
                    />
                    <InputError :message="answerForm.errors.body" />
                    <PrimaryButton :disabled="answerForm.processing">{{ $t('answers.submit') }}</PrimaryButton>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
