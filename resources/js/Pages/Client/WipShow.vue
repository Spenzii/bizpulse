<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    wip: Object,
});
</script>

<template>
    <Head :title="`Project: ${wip.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link :href="route('client-portal.dashboard')" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ wip.name }}</h2>
                </div>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold uppercase tracking-wider">{{ wip.status }}</span>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Overview Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-3xl p-8 border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row justify-between gap-8">
                        <div class="flex-1">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Description</h3>
                            <p class="text-lg text-gray-700 dark:text-gray-300">{{ wip.description || 'Project details are being updated by our team.' }}</p>
                        </div>
                        <div class="w-full md:w-48 space-y-4">
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Target Date</h3>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ wip.due_date }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Blockers if any -->
                    <div v-if="wip.blockers.filter(b => b.status === 'Open').length > 0" class="mt-8 p-6 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-100 dark:border-red-900/50">
                        <div class="flex items-center gap-3 text-red-700 dark:text-red-400 font-bold mb-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Current Blockers
                        </div>
                        <ul class="space-y-2">
                            <li v-for="blocker in wip.blockers.filter(b => b.status === 'Open')" :key="blocker.id" class="text-sm text-red-600 dark:text-red-300">
                                • {{ blocker.description }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Timeline / Updates -->
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                        Progress Timeline
                        <div class="h-px bg-gray-200 dark:bg-gray-700 flex-1"></div>
                    </h3>

                    <div class="space-y-6 relative before:absolute before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100 dark:before:bg-gray-800">
                        <div v-for="update in wip.updates" :key="update.id" class="pl-10 relative">
                            <div class="absolute left-0 top-1.5 w-8 h-8 bg-white dark:bg-gray-800 border-2 border-indigo-500 rounded-full flex items-center justify-center">
                                <div class="w-2 h-2 bg-indigo-500 rounded-full"></div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-gray-400">{{ new Date(update.created_at).toLocaleDateString() }}</span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300">{{ update.note }}</p>
                            </div>
                        </div>
                        <div v-if="wip.updates.length === 0" class="pl-10 text-gray-500 italic">
                            Initial project setup completed. Further updates will appear here as we progress.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
