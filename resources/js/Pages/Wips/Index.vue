<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    wips: Array,
    filters: Object,
    stats: Object,
    users: Array,
    clients: Array,
});

const search = ref(props.filters.search);
const currentStatus = ref(props.filters.status || 'Active');
const assignedToId = ref(props.filters.assigned_to_id || '');
const clientId = ref(props.filters.client_id || '');

const updateFilters = () => {
    router.get(route('wips.index'), {
        status: currentStatus.value,
        search: search.value,
        assigned_to_id: assignedToId.value,
        client_id: clientId.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([search, assignedToId, clientId], debounce(() => {
    updateFilters();
}, 300));

const setStatus = (status) => {
    currentStatus.value = status;
    updateFilters();
};

const clearFilters = () => {
    search.value = '';
    assignedToId.value = '';
    clientId.value = '';
    currentStatus.value = 'Active';
    updateFilters();
};
const selectedIds = ref([]);
const showBulkAssignModal = ref(false);
const bulkAssignedToId = ref('');

const toggleSelection = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(i => i !== id);
    } else {
        selectedIds.value.push(id);
    }
};

const toggleAll = () => {
    if (selectedIds.value.length === props.wips.length) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.wips.map(w => w.id);
    }
};

const runBulkAssign = () => {
    if (!bulkAssignedToId.value) return;
    
    router.post(route('wips.bulk-assign'), {
        ids: selectedIds.value,
        assigned_to_id: bulkAssignedToId.value
    }, {
        onSuccess: () => {
            selectedIds.value = [];
            showBulkAssignModal.value = false;
            bulkAssignedToId.value = '';
        }
    });
};

const runBulkDuplicate = () => {
    if (confirm(`Roll over ${selectedIds.value.length} selected jobs to next month?`)) {
        router.post(route('wips.bulk-duplicate'), {
            ids: selectedIds.value
        }, {
            onSuccess: () => {
                selectedIds.value = [];
            }
        });
    }
};
</script>

<template>
    <Head title="WIP Tracker" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">WIP Tracker</h2>
                <div class="flex gap-2">
                    <a
                        :href="route('exports.wips')"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 font-medium"
                    >
                        Export CSV
                    </a>
                    <Link
                        :href="route('wips.create')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium"
                    >
                        Add Work Item
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters & Tabs -->
                <div class="mb-6 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
                            <button
                                v-for="(count, status) in stats"
                                :key="status"
                                @click="setStatus(status)"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium transition-colors',
                                    currentStatus === status
                                        ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'
                                ]"
                            >
                                {{ status }} ({{ count }})
                            </button>
                        </div>

                        <div class="relative w-full md:w-64">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search client or job..."
                                class="w-full pl-10 pr-4 py-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            />
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="flex flex-wrap items-center gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase">Staff:</label>
                            <select v-model="assignedToId" class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm min-w-40">
                                <option value="">All Professionals</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase">Client:</label>
                            <select v-model="clientId" class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm min-w-40">
                                <option value="">All Clients</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>

                        <button 
                            @click="clearFilters" 
                            class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium ml-auto"
                        >
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <transition
                    enter-active-class="transform transition ease-out duration-300"
                    enter-from-class="translate-y-full opacity-0"
                    enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transform transition ease-in duration-200"
                    leave-from-class="translate-y-0 opacity-100"
                    leave-to-class="translate-y-full opacity-0"
                >
                    <div v-if="selectedIds.length > 0" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 bg-indigo-900 text-white px-6 py-4 rounded-2xl shadow-2xl border border-indigo-700/50 backdrop-blur-md">
                        <div class="pr-4 border-r border-indigo-700/50 text-sm font-bold">
                            {{ selectedIds.length }} Selected
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <select v-model="bulkAssignedToId" class="text-xs bg-indigo-800 border-indigo-700 rounded-lg text-white focus:ring-indigo-500">
                                <option value="">Re-assign to...</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <button 
                                @click="runBulkAssign"
                                :disabled="!bulkAssignedToId"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-xs font-bold rounded-lg transition-colors"
                            >
                                Apply
                            </button>
                        </div>

                        <button 
                            @click="runBulkDuplicate"
                            class="px-3 py-1.5 bg-green-600 hover:bg-green-500 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Roll Over to Next Month
                        </button>

                        <button 
                            @click="selectedIds = []"
                            class="ml-2 text-indigo-300 hover:text-white transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </transition>

                <!-- WIP Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input 
                                            type="checkbox" 
                                            :checked="selectedIds.length === wips.length && wips.length > 0"
                                            @change="toggleAll"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Job Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="wip in wips" :key="wip.id" 
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/10': selectedIds.includes(wip.id) }"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input 
                                            type="checkbox" 
                                            :checked="selectedIds.includes(wip.id)"
                                            @change="toggleSelection(wip.id)"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ wip.client.name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ wip.name }}</div>
                                            <span v-if="wip.is_recurring" class="px-1.5 py-0.5 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-[10px] font-bold rounded flex items-center gap-0.5">
                                                <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                Recurring
                                            </span>
                                        </div>
                                        <div v-if="wip.next_action" class="text-xs text-gray-500 mt-1">Next: {{ wip.next_action }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ wip.assigned_to?.name || 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': wip.status === 'Active',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': wip.status === 'Blocked',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': wip.status === 'Completed'
                                            }"
                                        >
                                            {{ wip.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="new Date(wip.due_date) < new Date() ? 'text-red-500 font-bold' : 'text-gray-500'">
                                        {{ wip.due_date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium border-l-0">
                                        <Link :href="route('wips.show', wip.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">View</Link>
                                    </td>
                                </tr>
                                <tr v-if="wips.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No work items found matching these filters.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
