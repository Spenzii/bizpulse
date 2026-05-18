<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PG', { style: 'currency', currency: 'PGK' }).format(value);
};
</script>

<template>
    <Head title="SaaS Admin Dashboard" />

    <AdminLayout>
        <template #header>
            SaaS Platform Overview
        </template>

        <div class="space-y-8">
            <!-- Row 1: Company Stats -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Company & Subscription Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <StatCard title="Total Companies" :value="stats.total_companies" color="indigo" />
                    <StatCard title="Active" :value="stats.active_companies" color="green" />
                    <StatCard title="Trial" :value="stats.trial_companies" color="blue" />
                    <StatCard title="Overdue" :value="stats.overdue_companies" color="orange" />
                    <StatCard title="Suspended" :value="stats.suspended_companies" color="red" />
                    <StatCard title="Cancelled" :value="stats.cancelled_companies" color="gray" />
                </div>
            </div>

            <!-- Row 2: Financial Stats -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Revenue & Payments (PGK)</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <StatCard title="Monthly Recurring Revenue" :value="formatCurrency(stats.monthly_recurring_revenue)" color="green" />
                    <StatCard title="Setup Fees Collected" :value="formatCurrency(stats.setup_fees_collected)" color="emerald" />
                    <StatCard title="Payments This Month" :value="formatCurrency(stats.payments_received_this_month)" color="teal" />
                    <StatCard title="Outstanding" :value="formatCurrency(stats.outstanding_payments)" color="rose" />
                </div>
            </div>

            <!-- Row 3: Growth & Support -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Users, Support & Leads</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <StatCard title="Total Users" :value="stats.total_users" color="purple" />
                    <StatCard title="Active Users (Mo)" :value="stats.active_users_this_month" color="violet" />
                    <StatCard title="Open Tickets" :value="stats.support_tickets_open" color="red" />
                    <StatCard title="New Signups (Mo)" :value="stats.new_signups_this_month" color="blue" />
                    <StatCard title="Website Leads" :value="stats.website_leads" color="cyan" />
                    <StatCard title="Demo Requests" :value="stats.demo_requests" color="sky" />
                </div>
            </div>

            <!-- Dashboard Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Recent Company Signups</h3>
                    <div class="text-sm text-gray-500">List of signups will appear here...</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Overdue Accounts</h3>
                    <div class="text-sm text-gray-500">List of overdue accounts will appear here...</div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
