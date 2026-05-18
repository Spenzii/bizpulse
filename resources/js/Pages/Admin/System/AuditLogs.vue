<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    logs: Array,
});
</script>

<template>
    <Head title="Audit Logs" />

    <AdminLayout>
        <template #header>Platform Activity & Audit Logs</template>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Timestamp</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Admin</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Action</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Target</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ log.created_at }}</td>
                        <td class="px-6 py-4 font-bold text-sm">{{ log.user.name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ log.action }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ log.target_type }} #{{ log.target_id }}
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ log.ip_address }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No audit logs found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
