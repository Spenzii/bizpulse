<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    leads: Array,
});
</script>

<template>
    <Head title="Website Leads" />

    <AdminLayout>
        <template #header>Website Inquiries & Leads</template>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Contact</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Company</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Message</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="lead in leads" :key="lead.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-xs text-gray-400">{{ lead.created_at }}</td>
                        <td class="px-6 py-4 font-bold text-sm">{{ lead.name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <p>{{ lead.email }}</p>
                            <p class="text-xs text-gray-400">{{ lead.phone }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ lead.company_name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600 max-w-xs truncate">{{ lead.message }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase">{{ lead.status }}</span>
                        </td>
                    </tr>
                    <tr v-if="leads.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No website leads yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
