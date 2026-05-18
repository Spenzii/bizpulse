<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    reportData: Array,
});
</script>

<template>
    <Head title="Staff Performance Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight">
                        Staff Performance Report
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Review staff velocity, active job loads, completed jobs, and fee realization rates.</p>
                </div>
                <!-- Navigation Toggle Submenu -->
                <div class="bg-gray-100 dark:bg-gray-800 p-1.5 rounded-2xl flex gap-1 border border-gray-200/50 dark:border-gray-700/50 shadow-inner">
                    <Link
                        :href="route('reports.financial')"
                        class="px-5 py-2 rounded-xl text-sm font-black transition-all"
                        :class="route().current('reports.financial') ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    >
                        Financial Insights
                    </Link>
                    <Link
                        :href="route('reports.relationship-owners')"
                        class="px-5 py-2 rounded-xl text-sm font-black transition-all"
                        :class="route().current('reports.relationship-owners') ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    >
                        Staff Performance
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Exports Panel -->
                <div class="flex justify-end">
                    <a
                        :href="route('exports.relationship-owners')"
                        class="px-5 py-2.5 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/80 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black border border-gray-200 dark:border-gray-700 transition flex items-center gap-2 shadow-sm"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Export Staff Performance CSV
                    </a>
                </div>

                <!-- Ledger Grid / Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700/50">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/30 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-4">Staff Member</th>
                                    <th class="px-6 py-4 text-center">Total Jobs</th>
                                    <th class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400">Active</th>
                                    <th class="px-6 py-4 text-center text-rose-600 dark:text-rose-400">Blocked</th>
                                    <th class="px-6 py-4 text-center text-indigo-600 dark:text-indigo-400">Completed</th>
                                    <th class="px-6 py-4 text-center">Completion Velocity</th>
                                    <th class="px-6 py-4 text-center">Fee Realization</th>
                                    <th class="px-6 py-4 text-right">Revenue Exposure</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="user in reportData" :key="user.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-all">
                                    <td class="px-6 py-4 font-medium whitespace-nowrap">
                                        <div class="font-black text-gray-900 dark:text-white">{{ user.name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ user.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-black text-gray-700 dark:text-gray-300">
                                        {{ user.total_jobs }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 text-xs font-black rounded-lg">
                                            {{ user.active_jobs }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span :class="[
                                            user.blocked_jobs > 0 
                                                ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 px-2 py-1 text-xs font-black rounded-lg' 
                                                : 'text-gray-400 dark:text-gray-500 font-medium'
                                        ]">
                                            {{ user.blocked_jobs }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 text-xs font-black rounded-lg">
                                            {{ user.completed_jobs }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="user.avg_completion_days !== null" class="font-mono font-bold text-gray-900 dark:text-white">
                                            {{ user.avg_completion_days }} days
                                        </span>
                                        <span v-else class="text-xs text-gray-400 italic">No data</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="user.realization_rate !== null" 
                                              class="px-2 py-1 text-xs font-black rounded-lg"
                                              :class="[
                                                  user.realization_rate >= 100 
                                                      ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400' 
                                                      : 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400'
                                              ]">
                                            {{ user.realization_rate }}%
                                        </span>
                                        <span v-else class="text-xs text-gray-400 italic">No data</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-black text-gray-950 dark:text-white">
                                        K{{ Number(user.revenue_at_risk).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                    </td>
                                </tr>
                                <tr v-if="reportData.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 italic">
                                        No staff members registered for this company.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
