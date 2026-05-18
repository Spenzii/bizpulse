<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    rule_quote: String,
});
</script>

<template>
    <Head title="Operating Rhythm - SOP Hub" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold leading-tight text-slate-800 dark:text-slate-100">
                    BizPulse Operating Rhythm
                </h2>
                <span class="px-3 py-1 rounded bg-teal-500/10 text-teal-600 dark:text-teal-400 text-xs font-bold border border-teal-500/20">
                    Official SME SOP v1.0
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Core Rule / Banner Callout -->
                <div class="bg-gradient-to-r from-teal-900/40 to-slate-900 border border-teal-800/80 rounded-2xl p-6 md:p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-[300px] h-[300px] bg-teal-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                    <div class="max-w-3xl relative z-10">
                        <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-2">Core Mandate</span>
                        <blockquote class="text-xl md:text-2xl font-black text-slate-100 italic leading-snug mb-4">
                            "{{ rule_quote }}"
                        </blockquote>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            WhatsApp, phone calls, and SMS can be used for rapid alerts, but <strong>every official instruction, deliverable, or task</strong> must be logged in BizPulse as a WIP/Job to count toward billing and performance analysis.
                        </p>
                    </div>
                </div>

                <!-- Operating Rhythm Loops Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Daily Rhythm Loops -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 font-bold text-lg">☀️</span>
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-slate-100">The Daily Loop</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Maintained daily by all operational staff</p>
                            </div>
                        </div>

                        <!-- Daily Timeline -->
                        <div class="space-y-6 relative border-l-2 border-slate-100 dark:border-slate-800 ml-4 pl-6">
                            <!-- Step 1 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-teal-500 text-slate-950 font-mono font-bold text-xs">1</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">09:00 AM — Morning Standup / Huddle</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-2">
                                    Team leads open the WIP/Jobs dashboard. Each supervisor and staff member answers three quick questions:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-slate-500 dark:text-slate-400 space-y-1 pl-2">
                                    <li>What was completed yesterday?</li>
                                    <li>What is my priority focus today?</li>
                                    <li>Are there any active blockers holding me up?</li>
                                </ul>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-100 font-mono font-bold text-xs">2</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">During the Day — Real-Time Operations</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Staff work on priority WIP items. If a blocker occurs, flag it as <strong>Blocked</strong> immediately inside the dashboard so supervisors are notified. Do not keep blockers secret.
                                </p>
                            </div>

                            <!-- Step 3 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-100 font-mono font-bold text-xs">3</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">04:45 PM — Status Flush</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-2">
                                    Every team member logs into BizPulse and completes their evening updates:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-slate-500 dark:text-slate-400 space-y-1 pl-2">
                                    <li>Increment progress percentages on active WIP items.</li>
                                    <li>Input the exact next physical action to be taken tomorrow.</li>
                                    <li>Enter any outstanding WhatsApp instructions received during the day as formal tasks.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Rhythm Loops -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-lg">🗓️</span>
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-slate-100">The Weekly Loop</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Maintained weekly by managers and leadership</p>
                            </div>
                        </div>

                        <!-- Weekly Timeline -->
                        <div class="space-y-6 relative border-l-2 border-slate-100 dark:border-slate-800 ml-4 pl-6">
                            <!-- Step 1 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500 text-slate-950 font-mono font-bold text-xs">1</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">Monday 10:00 AM — Management Analytics Review</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Management reviews the <strong>Reports Panel</strong>. Inspect the custom aged receivables trial aging ledger, monitor revenue trend lines, audit staff average completion velocity, and review high-risk blocker items.
                                </p>
                            </div>

                            <!-- Step 2 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-100 font-mono font-bold text-xs">2</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">Wednesday — Client CRM Touchpoints</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Review the CRM Client register. Identify accounts that have exceeded their target 7-day or 30-day contact threshold and schedule outreach campaigns.
                                </p>
                            </div>

                            <!-- Step 3 -->
                            <div class="relative">
                                <span class="absolute left-[-32px] top-0 flex items-center justify-center w-6 h-6 rounded-full bg-slate-800 dark:bg-slate-700 text-slate-100 font-mono font-bold text-xs">3</span>
                                <strong class="text-sm font-bold text-slate-800 dark:text-slate-200 block mb-1">Friday 03:00 PM — Weekly Review & Invoicing</strong>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-2">
                                    Managers open the <strong>Weekly Review Panel</strong> and click <strong>Billing Readiness</strong>:
                                </p>
                                <ul class="list-disc list-inside text-[11px] text-slate-500 dark:text-slate-400 space-y-1 pl-2">
                                    <li>Mark all completed WIP items as **Ready to Invoice**.</li>
                                    <li>Generate invoices and deliver branded PDFs instantly to client email boxes.</li>
                                    <li>Export invoice logs directly into Xero/MYOB CSV templates for bulk reconciliation.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOP Accountability Checklist Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-4">SME Accountability Checklist</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                            <span class="text-xl mb-2 block">📱</span>
                            <strong class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">No WhatsApp Silos</strong>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-normal">
                                All work messages must trigger a corresponding task record. If a client texts a new instruction, log it.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                            <span class="text-xl mb-2 block">⏱️</span>
                            <strong class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Due Date Discipline</strong>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-normal">
                                Every job must have a clear target completion date. If dates change, log a brief completion note or explanation.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                            <span class="text-xl mb-2 block">🚨</span>
                            <strong class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">No Silent Blockers</strong>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-normal">
                                Flag blocked tasks immediately. Management cannot resolve roadblocks they do not know exist.
                            </p>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40">
                            <span class="text-xl mb-2 block">💸</span>
                            <strong class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Billing Capture</strong>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-normal">
                                Always log accurate estimated fees for WIP items to allow exact pipeline forecasting in reports.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
