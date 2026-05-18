<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    metrics: Object,
    agingBuckets: Object,
    billingTrend: Array,
    outstandingLedger: Array,
});

// Tab state
const activeTab = ref('financial'); 

// Chart interactivity
const hoveredLineIndex = ref(null);
const hoveredBarIndex = ref(null);

// SVG Line Chart Computations (6-Month trend)
const svgWidth = 600;
const svgHeight = 240;
const paddingX = 60;
const paddingY = 40;

const maxBilledAmount = computed(() => {
    const amounts = props.billingTrend.map(t => t.amount);
    const max = Math.max(...amounts, 5000); // Minimum ceiling of 5000 for display
    return Math.ceil(max / 1000) * 1000;
});

const linePoints = computed(() => {
    if (!props.billingTrend || props.billingTrend.length === 0) return [];
    
    const maxVal = maxBilledAmount.value;
    const chartW = svgWidth - 2 * paddingX;
    const chartH = svgHeight - 2 * paddingY;
    
    return props.billingTrend.map((t, idx) => {
        const x = paddingX + idx * (chartW / (props.billingTrend.length - 1));
        const y = svgHeight - paddingY - (t.amount / maxVal) * chartH;
        return { x, y, label: t.month, amount: t.amount };
    });
});

const pathD = computed(() => {
    const pts = linePoints.value;
    if (pts.length === 0) return '';
    return pts.map((p, idx) => `${idx === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
});

const areaD = computed(() => {
    const pts = linePoints.value;
    if (pts.length === 0) return '';
    const path = pathD.value;
    const firstX = pts[0].x;
    const lastX = pts[pts.length - 1].x;
    const baseY = svgHeight - paddingY;
    return `${path} L ${lastX} ${baseY} L ${firstX} ${baseY} Z`;
});

// SVG Bar Chart Computations (Aging)
const agingData = computed(() => [
    { label: '0-30 Days', amount: props.agingBuckets.current, color: '#10b981', hoverColor: '#34d399' }, // Emerald
    { label: '31-60 Days', amount: props.agingBuckets.bracket_30_60, color: '#f59e0b', hoverColor: '#fbbf24' }, // Amber
    { label: '61-90 Days', amount: props.agingBuckets.bracket_60_90, color: '#f97316', hoverColor: '#fb923c' }, // Orange
    { label: '90+ Days', amount: props.agingBuckets.bracket_90_plus, color: '#ef4444', hoverColor: '#f87171' }, // Red
]);

const maxAgingAmount = computed(() => {
    const amounts = agingData.value.map(a => a.amount);
    const max = Math.max(...amounts, 1000);
    return Math.ceil(max / 1000) * 1000;
});

const barPoints = computed(() => {
    const maxVal = maxAgingAmount.value;
    const chartW = svgWidth - 2 * paddingX;
    const chartH = svgHeight - 2 * paddingY;
    const barWidth = 40;
    
    return agingData.value.map((b, idx) => {
        const x = paddingX + idx * (chartW / 3) - barWidth / 2;
        const h = (b.amount / maxVal) * chartH;
        const y = svgHeight - paddingY - h;
        return {
            ...b,
            x,
            y,
            w: barWidth,
            h: h > 0 ? h : 4, // Min height of 4px for empty bars
            midX: x + barWidth / 2
        };
    });
});

// Helper formatters
const formatCurrency = (val) => {
    return 'K' + parseFloat(val).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>

<template>
    <Head title="Financial Performance Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight">
                        Firm Financial Analytics
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Real-time invoicing statistics, aging ledger, and dynamic trend analysis.</p>
                </div>
                <!-- Navigation Toggle Submenu -->
                <div class="bg-gray-100 dark:bg-gray-800 p-1.5 rounded-2xl flex gap-1 border border-gray-200/50 dark:border-gray-700/50 shadow-inner">
                    <Link
                        :href="route('reports.financial')"
                        class="px-5 py-2 rounded-xl text-sm font-black transition-all"
                        :class="route().current('reports.financial') ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    >
                        Financial Insights
                    </Link>
                    <Link
                        :href="route('reports.relationship-owners')"
                        class="px-5 py-2 rounded-xl text-sm font-black transition-all"
                        :class="route().current('reports.relationship-owners') ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                    >
                        Staff Performance
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Exports Panel -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="font-black text-lg text-gray-900 dark:text-white">Accounting Integration</h3>
                        <p class="text-xs text-gray-400 mt-1">Sync your billing and invoice logs seamlessly with external tools.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a
                            :href="route('exports.financial')"
                            class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:hover:bg-gray-900/80 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black border border-gray-200 dark:border-gray-700 transition flex items-center gap-2 shadow-sm"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export Billing Ledger (CSV)
                        </a>
                        <a
                            :href="route('exports.xero')"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-lg shadow-indigo-500/20 transition flex items-center gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>
                            Download Xero / MYOB CSV
                        </a>
                    </div>
                </div>

                <!-- 1. Key Metrics Overview Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Billed Revenue -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col justify-between h-40 relative overflow-hidden group">
                        <div class="text-xs font-black uppercase tracking-widest text-gray-400">Total Billed (YTD)</div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white font-mono mt-2">{{ formatCurrency(metrics.total_billed) }}</div>
                        <div class="text-[10px] text-emerald-500 font-bold mt-2 flex items-center gap-1">
                            <span class="bg-emerald-500/10 p-0.5 rounded-full">✓</span> Formally Invoiced Sales
                        </div>
                        <div class="absolute right-4 bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                            <svg class="h-16 w-16 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        </div>
                    </div>

                    <!-- Total Collected -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col justify-between h-40 relative overflow-hidden group">
                        <div class="text-xs font-black uppercase tracking-widest text-gray-400">Total Realized Revenue</div>
                        <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-2">{{ formatCurrency(metrics.total_collected) }}</div>
                        <div class="text-[10px] text-gray-400 font-bold mt-2">
                            Realization Rate: <span class="font-bold text-gray-900 dark:text-white">{{ metrics.total_billed > 0 ? Math.round((metrics.total_collected / metrics.total_billed) * 100) : 0 }}%</span> of billed
                        </div>
                        <div class="absolute right-4 bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                            <svg class="h-16 w-16 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>

                    <!-- Accounts Receivable -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col justify-between h-40 relative overflow-hidden group">
                        <div class="text-xs font-black uppercase tracking-widest text-gray-400">Accounts Receivable</div>
                        <div class="text-3xl font-black text-amber-500 font-mono mt-2">{{ formatCurrency(metrics.outstanding_receivables) }}</div>
                        <div class="text-[10px] text-gray-400 font-bold mt-2 flex justify-between items-center pr-4">
                            <span>Pending GST: {{ formatCurrency(metrics.tax_liability) }}</span>
                        </div>
                        <div class="absolute right-4 bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                            <svg class="h-16 w-16 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                    </div>

                    <!-- Revenue Pipeline -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 flex flex-col justify-between h-40 relative overflow-hidden group">
                        <div class="text-xs font-black uppercase tracking-widest text-gray-400">WIP Pipeline Forecast</div>
                        <div class="text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono mt-2">{{ formatCurrency(metrics.ready_to_invoice_wip_value + metrics.active_wip_pipeline) }}</div>
                        <div class="text-[10px] text-gray-400 font-bold mt-2">
                            Ready: <span class="text-gray-900 dark:text-white font-bold">{{ formatCurrency(metrics.ready_to_invoice_wip_value) }}</span>
                        </div>
                        <div class="absolute right-4 bottom-4 opacity-5 group-hover:scale-110 transition duration-500">
                            <svg class="h-16 w-16 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                    </div>
                </div>

                <!-- 2. Dual Dynamic Charts Panel -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Line Chart: Billing Trend -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-black text-lg text-gray-900 dark:text-white">Historical Billing Trend</h3>
                                <p class="text-xs text-gray-400">Total invoiced sales over the last 6 months.</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px] font-black uppercase tracking-wider">Line Chart</span>
                        </div>

                        <!-- Custom Responsive SVG Line Chart -->
                        <div class="relative w-full aspect-[2.5/1] overflow-visible">
                            <svg :viewBox="`0 0 ${svgWidth} ${svgHeight}`" class="w-full h-full overflow-visible">
                                <defs>
                                    <!-- Linear gradient for filled area under the line -->
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#4f46e5" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#4f46e5" stop-opacity="0.00" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Y Axis Grid lines -->
                                <g stroke="#e5e7eb" stroke-dasharray="4 4" stroke-width="1" class="dark:stroke-gray-700">
                                    <line v-for="i in 4" :key="i"
                                          :x1="paddingX" 
                                          :y1="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3)" 
                                          :x2="svgWidth - paddingX" 
                                          :y2="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3)" />
                                </g>

                                <!-- Y Axis Labels -->
                                <g fill="#9ca3af" font-size="10" font-family="monospace" text-anchor="end" class="dark:fill-gray-500 font-bold">
                                    <text v-for="i in 4" :key="i"
                                          :x="paddingX - 10" 
                                          :y="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3) + 3">
                                        {{ Math.round((maxBilledAmount - (i - 1) * (maxBilledAmount / 3))) }}
                                    </text>
                                </g>

                                <!-- Filled Gradient Area Under Line -->
                                <path :d="areaD" fill="url(#chartGradient)" />

                                <!-- Main Billing Trend Curve Line -->
                                <path :d="pathD" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                                <!-- Line Dots with Hover States -->
                                <g>
                                    <circle v-for="(pt, idx) in linePoints" :key="idx"
                                            :cx="pt.x" 
                                            :cy="pt.y" 
                                            :r="hoveredLineIndex === idx ? 8 : 5" 
                                            :fill="hoveredLineIndex === idx ? '#4f46e5' : '#ffffff'" 
                                            stroke="#4f46e5" 
                                            stroke-width="3" 
                                            class="cursor-pointer transition-all duration-200"
                                            @mouseenter="hoveredLineIndex = idx"
                                            @mouseleave="hoveredLineIndex = null" />
                                </g>

                                <!-- X Axis Labels -->
                                <g fill="#9ca3af" font-size="10" text-anchor="middle" class="dark:fill-gray-500 font-bold">
                                    <text v-for="(pt, idx) in linePoints" :key="idx"
                                          :x="pt.x" 
                                          :y="svgHeight - paddingY + 20">
                                        {{ pt.label }}
                                    </text>
                                </g>
                            </svg>

                            <!-- Tooltip Overlay -->
                            <div v-if="hoveredLineIndex !== null" 
                                 class="absolute bg-gray-900 text-white dark:bg-white dark:text-gray-900 px-3 py-2 rounded-xl text-xs font-black shadow-xl pointer-events-none transition-all duration-150 border border-white/10 dark:border-gray-200"
                                 :style="{
                                     left: `${(linePoints[hoveredLineIndex].x / svgWidth) * 100}%`,
                                     top: `${(linePoints[hoveredLineIndex].y / svgHeight) * 100 - 20}%`,
                                     transform: 'translate(-50%, -100%)'
                                 }">
                                <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500">{{ linePoints[hoveredLineIndex].label }}</div>
                                <div class="font-mono mt-0.5">{{ formatCurrency(linePoints[hoveredLineIndex].amount) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bar Chart: Aged Trial Balance -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-black text-lg text-gray-900 dark:text-white">Aged Trial Balance (Aging)</h3>
                                <p class="text-xs text-gray-400">Brackets showing risk profiles of unpaid outstanding invoices.</p>
                            </div>
                            <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-500 rounded-full text-[10px] font-black uppercase tracking-wider">Risk profile</span>
                        </div>

                        <!-- Custom Responsive SVG Bar Chart -->
                        <div class="relative w-full aspect-[2.5/1] overflow-visible">
                            <svg :viewBox="`0 0 ${svgWidth} ${svgHeight}`" class="w-full h-full overflow-visible">
                                <!-- Y Axis Grid lines -->
                                <g stroke="#e5e7eb" stroke-dasharray="4 4" stroke-width="1" class="dark:stroke-gray-700">
                                    <line v-for="i in 4" :key="i"
                                          :x1="paddingX" 
                                          :y1="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3)" 
                                          :x2="svgWidth - paddingX" 
                                          :y2="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3)" />
                                </g>

                                <!-- Y Axis Labels -->
                                <g fill="#9ca3af" font-size="10" font-family="monospace" text-anchor="end" class="dark:fill-gray-500 font-bold">
                                    <text v-for="i in 4" :key="i"
                                          :x="paddingX - 10" 
                                          :y="paddingY + (i - 1) * ((svgHeight - 2 * paddingY) / 3) + 3">
                                        {{ Math.round((maxAgingAmount - (i - 1) * (maxAgingAmount / 3))) }}
                                    </text>
                                </g>

                                <!-- Bar columns with dynamic rounded rects -->
                                <g>
                                    <rect v-for="(bar, idx) in barPoints" :key="idx"
                                          :x="bar.x" 
                                          :y="bar.y" 
                                          :width="bar.w" 
                                          :height="bar.h" 
                                          rx="6" 
                                          ry="6"
                                          :fill="hoveredBarIndex === idx ? bar.hoverColor : bar.color" 
                                          class="cursor-pointer transition-all duration-200"
                                          @mouseenter="hoveredBarIndex = idx"
                                          @mouseleave="hoveredBarIndex = null" />
                                </g>

                                <!-- X Axis Labels -->
                                <g fill="#9ca3af" font-size="10" text-anchor="middle" class="dark:fill-gray-500 font-bold">
                                    <text v-for="(bar, idx) in barPoints" :key="idx"
                                          :x="bar.midX" 
                                          :y="svgHeight - paddingY + 20">
                                        {{ bar.label }}
                                    </text>
                                </g>
                            </svg>

                            <!-- Tooltip Overlay -->
                            <div v-if="hoveredBarIndex !== null" 
                                 class="absolute bg-gray-900 text-white dark:bg-white dark:text-gray-900 px-3 py-2 rounded-xl text-xs font-black shadow-xl pointer-events-none transition-all duration-150 border border-white/10 dark:border-gray-200"
                                 :style="{
                                     left: `${(barPoints[hoveredBarIndex].midX / svgWidth) * 100}%`,
                                     top: `${(barPoints[hoveredBarIndex].y / svgHeight) * 100 - 20}%`,
                                     transform: 'translate(-50%, -100%)'
                                 }">
                                <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500">{{ barPoints[hoveredBarIndex].label }}</div>
                                <div class="font-mono mt-0.5">{{ formatCurrency(barPoints[hoveredBarIndex].amount) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Accounts Receivable / Outstanding Ledger -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700/50 flex justify-between items-center">
                        <div>
                            <h3 class="font-black text-lg text-gray-900 dark:text-white">Outstanding Invoices Ledger</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Tracking sent invoices currently requiring collection.</p>
                        </div>
                        <span class="px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-500 rounded-full text-xs font-bold font-mono">
                            {{ outstandingLedger.length }} Invoices Pending
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/30 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-4">Invoice #</th>
                                    <th class="px-6 py-4">Client Name</th>
                                    <th class="px-6 py-4">Issue Date</th>
                                    <th class="px-6 py-4">Due Date</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center">Days Aging</th>
                                    <th class="px-6 py-4 text-right">Amount (PGK)</th>
                                    <th class="px-6 py-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="invoice in outstandingLedger" :key="invoice.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-all">
                                    <td class="px-6 py-4 font-black text-indigo-600 dark:text-indigo-400">
                                        {{ invoice.invoice_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ invoice.client.name }}</div>
                                        <div class="text-xs text-gray-400">{{ invoice.client.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ new Date(invoice.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ new Date(invoice.due_date).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 text-[10px] font-black uppercase rounded-full">
                                            {{ invoice.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="invoice.days_overdue > 0" 
                                              class="px-2.5 py-1 text-[10px] font-black uppercase rounded-full"
                                              :class="[
                                                  invoice.days_overdue >= 90 ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' :
                                                  invoice.days_overdue >= 60 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' :
                                                  'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                                              ]">
                                            {{ invoice.days_overdue }} Days Overdue
                                        </span>
                                        <span v-else class="text-xs text-gray-400 font-medium">Within terms</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-black text-gray-950 dark:text-white">
                                        {{ formatCurrency(invoice.total_amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Link 
                                            :href="route('invoices.show', invoice.id)"
                                            class="px-3.5 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:hover:bg-gray-900/80 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-bold transition-all inline-block"
                                        >
                                            View Invoice
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="outstandingLedger.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 font-medium italic">
                                        Awesome! No outstanding unpaid invoices found on your account.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
