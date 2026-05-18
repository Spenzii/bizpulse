<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    tickets: Array,
});

const selectedTicket = ref(null);
const form = useForm({
    status: '',
    priority: '',
});

const viewTicket = (ticket) => {
    selectedTicket.value = ticket;
    form.status = ticket.status;
    form.priority = ticket.priority;
};

const updateTicket = () => {
    form.patch(route('admin.tickets.update', selectedTicket.value.id), {
        onSuccess: () => selectedTicket.value = null,
    });
};
</script>

<template>
    <Head title="Support Tickets" />

    <AdminLayout>
        <template #header>SaaS Platform Support</template>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Priority</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Subject</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Company</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span :class="{
                                'bg-green-100 text-green-600': ticket.status === 'Resolved',
                                'bg-blue-100 text-blue-600': ticket.status === 'Open',
                                'bg-gray-100 text-gray-600': ticket.status === 'Closed'
                            }" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase">{{ ticket.status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="{
                                'text-red-500': ticket.priority === 'Urgent' || ticket.priority === 'High',
                                'text-orange-500': ticket.priority === 'Medium',
                                'text-gray-400': ticket.priority === 'Low'
                            }" class="text-xs font-bold">{{ ticket.priority }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-sm">{{ ticket.subject }}</td>
                        <td class="px-6 py-4 text-sm">{{ ticket.company.name }}</td>
                        <td class="px-6 py-4 text-xs text-gray-400">{{ ticket.created_at }}</td>
                        <td class="px-6 py-4 text-right">
                            <button @click="viewTicket(ticket)" class="text-indigo-600 font-bold text-xs hover:underline">View</button>
                        </td>
                    </tr>
                    <tr v-if="tickets.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No support tickets found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ticket Modal -->
        <div v-if="selectedTicket" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold">Ticket #{{ selectedTicket.id }}</h3>
                    <button @click="selectedTicket = null" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Subject</span>
                        <p class="text-lg font-bold">{{ selectedTicket.subject }}</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Description</span>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm leading-relaxed">{{ selectedTicket.description }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Status</label>
                            <select v-model="form.status" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500">
                                <option>Open</option>
                                <option>Pending</option>
                                <option>Resolved</option>
                                <option>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Priority</label>
                            <select v-model="form.priority" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500">
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Urgent</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end gap-3">
                    <button @click="selectedTicket = null" class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                    <button @click="updateTicket" class="px-6 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Update Ticket</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
