<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="System Settings" />

    <AdminLayout>
        <template #header>SaaS System Settings</template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Platform Configuration</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-mono">Platform Name</label>
                        <input type="text" value="BizPulse SaaS" class="w-full border-gray-200 rounded-lg bg-gray-50" readonly />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1 font-mono">Support Email</label>
                        <input type="text" value="support@penguinpacificltd.com" class="w-full border-gray-200 rounded-lg" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Backup & Storage</h3>
                <div class="space-y-6">
                    <div class="flex justify-between items-center bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                        <div>
                            <p class="font-bold text-indigo-700">DigitalOcean Spaces</p>
                            <p class="text-xs text-indigo-500">Active Storage Provider</p>
                        </div>
                        <span class="px-3 py-1 bg-green-500 text-white text-[10px] font-black uppercase rounded-full">Connected</span>
                    </div>
                    <button class="w-full py-3 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-900 transition-all shadow-md">
                        Run Manual Database Backup
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
