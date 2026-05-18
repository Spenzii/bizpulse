<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    invoice: Object,
});

const printInvoice = () => {
    window.print();
};

const updateStatus = (newStatus) => {
    router.patch(route('invoices.status.update', props.invoice.id), {
        status: newStatus
    });
};
</script>

<template>
    <Head :title="`Invoice ${invoice.invoice_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center print:hidden">
                <div class="flex items-center gap-4">
                    <Link :href="route('invoices.index')" class="text-sm text-indigo-600 hover:underline">← Back to Ledger</Link>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">View Invoice</h2>
                </div>
                <div class="flex gap-2">
                    <select 
                        @change="updateStatus($event.target.value)"
                        :value="invoice.status"
                        class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500"
                    >
                        <option>Draft</option>
                        <option>Sent</option>
                        <option>Paid</option>
                        <option>Cancelled</option>
                    </select>
                    <button 
                        @click="router.post(route('invoices.send', invoice.id))" 
                        class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-bold flex items-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Email to Client
                    </button>
                    <button @click="printInvoice" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                        Print / Save as PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Premium Invoice Container -->
                <div class="bg-white p-12 shadow-2xl rounded-sm border border-gray-100 min-h-[1056px] flex flex-col text-gray-900" id="invoice-content">
                    <div class="flex justify-between items-start border-b-4 pb-8" :style="{ borderBottomColor: invoice.company.primary_color || '#111827' }">
                        <div class="flex items-center gap-6">
                            <div v-if="invoice.company.logo_path" class="h-20 w-20 flex-shrink-0">
                                <img :src="`/storage/${invoice.company.logo_path}`" class="h-full w-full object-contain" />
                            </div>
                            <div>
                                <div class="text-3xl font-black tracking-tighter uppercase mb-1">{{ invoice.company.name }}</div>
                                <div class="text-sm text-gray-500 max-w-xs">
                                    {{ invoice.company.address_line_1 }}<br v-if="invoice.company.address_line_1">
                                    {{ invoice.company.address_line_2 }}<br v-if="invoice.company.address_line_2">
                                    <span v-if="invoice.company.tax_id" class="font-bold">TIN: {{ invoice.company.tax_id }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <h1 class="text-5xl font-black text-gray-100 uppercase leading-none mb-4">INVOICE</h1>
                            <div class="space-y-1">
                                <div class="text-sm font-bold"># {{ invoice.invoice_number }}</div>
                                <div class="text-sm text-gray-500 italic">Issued: {{ new Date(invoice.created_at).toLocaleDateString() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Client & Company Details -->
                    <div class="grid grid-cols-2 gap-12 mt-12 mb-12">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Bill To</div>
                            <div class="text-lg font-bold">{{ invoice.client.name }}</div>
                            <div class="text-sm text-gray-600 mt-1">
                                {{ invoice.client.contact_person }}<br>
                                {{ invoice.client.email }}<br>
                                {{ invoice.client.phone }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Due By</div>
                            <div class="text-xl font-black" :style="{ color: invoice.company.primary_color || '#4f46e5' }">{{ invoice.due_date }}</div>
                            <div v-if="invoice.status === 'Paid'" class="mt-4 inline-block px-4 py-1 border-2 border-green-600 text-green-600 font-black uppercase tracking-widest rotate-[-5deg]">
                                PAID IN FULL
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="flex-grow">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b-2 text-left" :style="{ borderBottomColor: invoice.company.primary_color || '#111827' }">
                                    <th class="py-4 text-xs font-black uppercase tracking-widest">Description</th>
                                    <th class="py-4 text-right text-xs font-black uppercase tracking-widest">Amount (PGK)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in invoice.items" :key="item.id" class="border-b border-gray-100">
                                    <td class="py-6 pr-8">
                                        <div class="font-bold text-gray-900">{{ item.description }}</div>
                                        <div v-if="item.wip" class="text-xs text-gray-400 mt-1">Completed Project Item</div>
                                    </td>
                                    <td class="py-6 text-right font-bold">
                                        K{{ parseFloat(item.amount).toLocaleString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary -->
                    <div class="mt-12 border-t-2 pt-8 flex justify-end" :style="{ borderTopColor: invoice.company.primary_color || '#111827' }">
                        <div class="w-full md:w-64 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-bold uppercase tracking-widest">Subtotal</span>
                                <span class="font-bold text-gray-900">K{{ (parseFloat(invoice.total_amount) - parseFloat(invoice.tax_amount)).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 font-bold uppercase tracking-widest">GST (10%)</span>
                                <span class="font-bold text-gray-900">K{{ parseFloat(invoice.tax_amount).toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-100">
                                <span class="text-lg font-black uppercase tracking-widest">Total Due</span>
                                <span class="text-2xl font-black" :style="{ color: invoice.company.primary_color || '#4f46e5' }">K{{ parseFloat(invoice.total_amount).toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer / Notes -->
                    <div class="mt-16 pt-12 border-t border-gray-100 text-[10px] text-gray-400 uppercase tracking-widest text-center">
                        <p class="mb-2">Thank you for your business. Payment is due within 14 days of issue.</p>
                        <p>{{ invoice.company.name }} — professional services platform</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    /* Hide layout elements on print */
    nav, header, .print\:hidden {
        display: none !important;
    }
    body {
        background-color: white !important;
    }
    .py-12 {
        padding: 0 !important;
    }
    .max-w-4xl {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .shadow-2xl {
        box-shadow: none !important;
    }
    .rounded-sm {
        border-radius: 0 !important;
    }
    .bg-white {
        background-color: white !important;
    }
    /* Ensure colors print correctly */
    .text-indigo-600 {
        color: #4f46e5 !important;
        -webkit-print-color-adjust: exact;
    }
    .border-indigo-500 {
        border-color: #6366f1 !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
