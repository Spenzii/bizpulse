<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    client: Object,
    active_wips: Array,
    completed_wips: Array,
});
</script>

<template>
    <Head title="Client Portal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <img v-if="client.company.logo_path" :src="`/storage/${client.company.logo_path}`" class="h-10 w-auto" />
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Welcome, {{ client.name }}</h2>
                </div>
                <div class="text-sm text-gray-500 font-medium italic">Transparency through {{ client.company.name }}</div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Status Header -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-white p-6 rounded-2xl shadow-xl transition-all duration-700" :style="{ backgroundColor: client.company.primary_color || '#4f46e5' }">
                        <div class="text-white/70 text-xs font-bold uppercase tracking-widest">Active Jobs</div>
                        <div class="text-4xl font-black mt-2">{{ active_wips.length }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <div class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-widest">Recently Completed</div>
                        <div class="text-4xl font-black mt-2 text-gray-900 dark:text-white">{{ completed_wips.length }}</div>
                    </div>
                    <div class="bg-emerald-500 text-white p-6 rounded-2xl shadow-xl">
                        <div class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Account Status</div>
                        <div class="text-2xl font-black mt-2">Active Professional</div>
                    </div>
                </div>

                <!-- Active Jobs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Progress</h3>
                        <span class="text-xs text-gray-400">Live updates from our team</span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        <div v-for="wip in active_wips" :key="wip.id" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-lg font-bold" :style="{ color: client.company.primary_color || '#4f46e5' }">{{ wip.name }}</h4>
                                        <span v-if="wip.status === 'Blocked'" class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">Action Required</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ wip.description || 'Ongoing project work.' }}</p>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="text-xs text-gray-400 uppercase font-bold">Target Date</div>
                                        <div class="text-sm font-semibold" :class="new Date(wip.due_date) < new Date() ? 'text-red-500' : 'text-gray-900 dark:text-white'">
                                            {{ wip.due_date }}
                                        </div>
                                    </div>
                                    <Link :href="route('client-portal.wips.show', wip.id)" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-sm font-bold transition-all">
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-if="active_wips.length === 0" class="p-12 text-center text-gray-500">
                            No active jobs found. We'll start as soon as your next project is ready!
                        </div>
                    </div>
                </div>

                <!-- Recently Completed -->
                <div v-if="completed_wips.length > 0" class="bg-gray-50 dark:bg-gray-900/50 p-8 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Achievement History</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="wip in completed_wips" :key="wip.id" class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm flex items-center gap-4">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white">{{ wip.name }}</div>
                                <div class="text-xs text-gray-500">Completed on {{ wip.completed_at?.split('T')[0] || wip.updated_at?.split('T')[0] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoices -->
                <div v-if="$page.props.invoices?.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Billing & Invoices</h3>
                    <div class="space-y-4">
                        <div v-for="invoice in $page.props.invoices" :key="invoice.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ invoice.invoice_number }}</div>
                                    <div class="text-xs text-gray-500">Due: {{ invoice.due_date }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="font-black text-gray-900 dark:text-white">K{{ parseFloat(invoice.total_amount).toLocaleString() }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest" :class="invoice.status === 'Paid' ? 'text-green-500' : 'text-gray-400'">{{ invoice.status }}</div>
                                </div>
                                <Link :href="route('invoices.show', invoice.id)" class="hover:underline text-sm font-bold" :style="{ color: client.company.primary_color || '#4f46e5' }">View</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
