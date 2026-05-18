<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    payments: Array,
    companies: Array,
});

const showModal = ref(false);
const form = useForm({
    company_id: '',
    amount: '',
    currency: 'PGK',
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: 'Bank Transfer',
    reference_number: '',
    type: 'Subscription',
    notes: '',
});

const submit = () => {
    form.post(route('admin.payments.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PG', { style: 'currency', currency: 'PGK' }).format(val);
</script>

<template>
    <Head title="Payments" />

    <AdminLayout>
        <template #header>SaaS Revenue & Payments</template>

        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Transaction History</h3>
            <button @click="showModal = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-md">
                + Record Payment
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Company</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Type</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Amount</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Method</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm">{{ payment.payment_date }}</td>
                        <td class="px-6 py-4 font-bold text-sm text-indigo-600">{{ payment.company.name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 text-[10px] font-bold uppercase rounded-md">{{ payment.type }}</span>
                        </td>
                        <td class="px-6 py-4 font-black text-sm">{{ formatCurrency(payment.amount) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ payment.payment_method }}</td>
                        <td class="px-6 py-4 text-xs font-mono text-gray-400">{{ payment.reference_number || 'N/A' }}</td>
                    </tr>
                    <tr v-if="payments.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No payments recorded yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold">Record Manual Payment</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Company</label>
                        <select v-model="form.company_id" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500">
                            <option value="" disabled>Select Company</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Amount (PGK)</label>
                            <input type="number" v-model="form.amount" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
                            <input type="date" v-model="form.payment_date" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Type</label>
                            <select v-model="form.type" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500">
                                <option>Subscription</option>
                                <option>Setup Fee</option>
                                <option>Training Fee</option>
                                <option>Custom</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Method</label>
                            <select v-model="form.payment_method" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500">
                                <option>Bank Transfer</option>
                                <option>EFTPOS</option>
                                <option>Cash</option>
                                <option>Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Reference Number</label>
                        <input type="text" v-model="form.reference_number" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500" placeholder="e.g. Receipt #12345" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Notes</label>
                        <textarea v-model="form.notes" rows="2" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Record Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
