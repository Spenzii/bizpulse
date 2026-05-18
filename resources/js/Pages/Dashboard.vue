<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    role: String,
    stats: Object,
    recent_wips: Array,
    my_tasks: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ role === 'company_admin' ? 'Company Manager Dashboard' : 'My Work Dashboard' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Company Admin View -->
                <div v-if="role === 'company_admin'">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active WIPs</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stats.active_wips }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Overdue Jobs</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stats.overdue_wips }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Open Blockers</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ stats.open_blockers }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue at Risk</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">PGK {{ Number(stats.revenue_at_risk).toLocaleString() }}</div>
                        </div>
                    </div>

                    <!-- Monthly Billing Performance Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Monthly Billing Performance</h3>
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 text-xs font-bold rounded-full uppercase">Current Month</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Billed This Month</div>
                                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                            PGK {{ Number(stats.billing_performance.current_month_billed).toLocaleString() }}
                                        </div>
                                        <div class="text-xs mt-1" :class="stats.billing_performance.current_month_billed >= stats.billing_performance.last_month_billed ? 'text-green-500' : 'text-red-500'">
                                            {{ stats.billing_performance.current_month_billed >= stats.billing_performance.last_month_billed ? '↑' : '↓' }}
                                            vs last month (PGK {{ Number(stats.billing_performance.last_month_billed).toLocaleString() }})
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="text-gray-500">Progress to Target</span>
                                            <span class="font-bold">PGK {{ Number(stats.billing_performance.target_billed).toLocaleString() }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" 
                                                 :style="{ width: Math.min((stats.billing_performance.current_month_billed / stats.billing_performance.target_billed) * 100, 100) + '%' }">
                                            </div>
                                        </div>
                                        <div class="text-[10px] text-right mt-1 text-gray-400">
                                            {{ Math.round((stats.billing_performance.current_month_billed / stats.billing_performance.target_billed) * 100) }}% Achieved
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 font-medium">Pipeline Value</div>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                        PGK {{ Number(stats.billing_performance.pipeline_value).toLocaleString() }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Potential revenue from active & blocked jobs
                                    </div>
                                </div>

                                <div class="flex flex-col justify-center items-center p-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                                    <div class="text-center">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Quick Action</div>
                                        <Link :href="route('wips.index', { status: 'Active' })" class="inline-flex items-center text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            View Active Pipeline
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent WIPs</h3>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Job Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Owner</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="wip in recent_wips" :key="wip.id">
                                        <td class="px-6 py-4 text-sm">{{ wip.client.name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ wip.name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ wip.assigned_to?.name || 'Unassigned' }}</td>
                                        <td class="px-6 py-4 text-sm text-red-500">{{ wip.due_date }}</td>
                                    </tr>
                                    <tr v-if="recent_wips.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No active work items found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Staff View -->
                <div v-else-if="role === 'staff'">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">My Tasks</h3>
                            <div v-for="task in my_tasks" :key="task.id" class="mb-4 p-4 border dark:border-gray-700 rounded-lg flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-indigo-600 dark:text-indigo-400">{{ task.client.name }}</div>
                                    <div class="text-lg font-semibold">{{ task.name }}</div>
                                    <div class="text-sm text-gray-500">Due: {{ task.due_date }}</div>
                                </div>
                                <div>
                                    <PrimaryButton>Update Status</PrimaryButton>
                                </div>
                            </div>
                            <div v-if="my_tasks.length === 0" class="text-center py-8 text-gray-500">
                                You have no open tasks. Great job!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Add any local styles here */
</style>
