<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    billable_wips: Array,
});

const selectedWips = ref([]);
const invoiceForm = useForm({
    client_id: null,
    wip_ids: [],
    due_date: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // Default 14 days
    notes: '',
});

const toggleSelection = (id) => {
    const index = selectedWips.value.indexOf(id);
    if (index > -1) {
        selectedWips.value.splice(index, 1);
    } else {
        selectedWips.value.push(id);
    }
};

const canGenerateInvoice = computed(() => {
    if (selectedWips.value.length === 0) return false;
    
    // Ensure all selected WIPs belong to the same client
    const firstWip = props.billable_wips.find(w => w.id === selectedWips.value[0]);
    if (!firstWip) return false;
    
    return selectedWips.value.every(id => {
        const wip = props.billable_wips.find(w => w.id === id);
        return wip.client_id === firstWip.client_id;
    });
});

const generateInvoice = () => {
    const firstWip = props.billable_wips.find(w => w.id === selectedWips.value[0]);
    invoiceForm.client_id = firstWip.client_id;
    invoiceForm.wip_ids = selectedWips.value;
    invoiceForm.post(route('invoices.store'), {
        onSuccess: () => {
            selectedWips.value = [];
        }
    });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PG', { style: 'currency', currency: 'PGK' }).format(val);

const updateStatus = (wip, status) => {
    const form = useForm({ billing_status: status });
    form.patch(route('company-billing.update', wip.id));
};
</script>

<template>
    <Head title="Billing Pipeline" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Project Billing Pipeline</h2>
                <Link :href="route('invoices.index')" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 font-medium">View Ledger</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Floating Batch Action Bar -->
                <div v-if="selectedWips.length > 0" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4">
                    <div class="bg-gray-900 text-white p-4 rounded-2xl shadow-2xl flex items-center justify-between border border-gray-700">
                        <div class="flex items-center gap-4">
                            <span class="bg-indigo-600 px-3 py-1 rounded-full text-xs font-black">{{ selectedWips.length }} Selected</span>
                            <span v-if="!canGenerateInvoice" class="text-xs text-red-400 font-bold">Please select jobs from the same client</span>
                            <span v-else class="text-xs text-green-400 font-bold">Ready to group into Invoice</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="selectedWips = []" class="px-3 py-1.5 text-xs text-gray-400 hover:text-white transition-colors">Clear</button>
                            <button 
                                :disabled="!canGenerateInvoice || invoiceForm.processing" 
                                @click="generateInvoice"
                                class="px-4 py-1.5 bg-indigo-600 rounded-xl text-xs font-black hover:bg-indigo-700 transition-all disabled:opacity-50"
                            >
                                Generate Professional Invoice
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/50">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Billable Work Items</h3>
                            <p class="text-xs text-gray-500 mt-1">Select completed jobs to generate professional client invoices.</p>
                        </div>
                    </div>
                    
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-900 uppercase text-[10px] font-black tracking-widest text-gray-400">
                            <tr>
                                <th class="px-6 py-4 w-10"></th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Job/WIP</th>
                                <th class="px-6 py-4">Est. Fee</th>
                                <th class="px-6 py-4">Billing Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="wip in billable_wips" :key="wip.id" 
                                :class="selectedWips.includes(wip.id) ? 'bg-indigo-50/50 dark:bg-indigo-900/20' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                class="transition-colors cursor-pointer"
                                @click="toggleSelection(wip.id)"
                            >
                                <td class="px-6 py-4">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedWips.includes(wip.id)"
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        @click.stop="toggleSelection(wip.id)"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white">{{ wip.client.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ wip.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-black text-sm text-gray-900 dark:text-white">{{ formatCurrency(wip.estimated_fee || 0) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400': wip.billing_status === 'Ready to Invoice',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': wip.billing_status === 'Invoiced',
                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': wip.billing_status === 'Paid',
                                    }" class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase">{{ wip.billing_status }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2 text-xs" @click.stop>
                                        <button v-if="wip.billing_status === 'Ready to Invoice'" @click="updateStatus(wip, 'Invoiced')" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Mark Invoiced</button>
                                        <button v-if="wip.billing_status === 'Invoiced'" @click="updateStatus(wip, 'Paid')" class="text-green-600 dark:text-green-400 font-bold hover:underline">Mark Paid</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="billable_wips.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No billable jobs found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
