<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reviews: Array,
});

const showModal = ref(false);
const form = useForm({
    summary: '',
});

const submit = () => {
    form.post(route('weekly-reviews.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Weekly Reviews" />

    <AuthenticatedLayout>
        <template #header>Weekly Operational Review</template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Review History</h3>
                        <p class="text-sm text-gray-500">Track your weekly performance and highlights.</p>
                    </div>
                    <button @click="showModal = true" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-md">
                        + Submit This Week's Review
                    </button>
                </div>

                <div class="space-y-4">
                    <div v-for="review in reviews" :key="review.id" class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-bold">Week {{ review.week_number }}, {{ review.year }}</h4>
                                <p class="text-xs text-gray-400">Submitted by {{ review.submitted_by.name }} on {{ review.created_at }}</p>
                            </div>
                        </div>
                        <div class="text-gray-700 whitespace-pre-wrap">{{ review.summary }}</div>
                    </div>
                    
                    <div v-if="reviews.length === 0" class="bg-white p-12 rounded-xl border border-gray-100 text-center text-gray-400">
                        No reviews submitted yet. Start tracking your progress today.
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold">Submit Weekly Review</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Weekly Summary & Highlights</label>
                        <p class="text-xs text-gray-500 mb-2">What did your team accomplish this week? Any major wins or blockers?</p>
                        <textarea v-model="form.summary" rows="8" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 shadow-sm" placeholder="Summarize the week's progress..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                        <button type="submit" class="px-8 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
