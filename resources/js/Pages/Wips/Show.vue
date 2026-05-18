<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    wip: Object,
    metrics: Object,
});

const showBlockerForm = ref(false);
const blockerForm = useForm({
    description: '',
    priority: 'Medium',
});

const activeTab = ref('details');

const showUpdateForm = ref(false);
const updateForm = useForm({
    content: '',
    percentage_completed: 0,
});

const submitBlocker = () => {
    blockerForm.post(route('wips.blockers.store', props.wip.id), {
        onSuccess: () => {
            blockerForm.reset();
            showBlockerForm.value = false;
        },
    });
};

const submitUpdate = () => {
    updateForm.post(route('wips.updates.store', props.wip.id), {
        onSuccess: () => {
            updateForm.reset();
            showUpdateForm.value = false;
        },
    });
};

const resolveBlocker = (blockerId) => {
    router.patch(route('blockers.resolve', blockerId), {}, {
        preserveScroll: true,
    });
};

const markAsCompleted = () => {
    if (confirm('Are you sure you want to mark this job as 100% complete?')) {
        router.post(route('wips.updates.store', props.wip.id), {
            content: 'Job marked as completed.',
            percentage_completed: 100,
        });
    }
};
</script>

<template>
    <Head :title="`Job: ${wip.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ wip.client.name }} / {{ wip.name }}
                </h2>
                <div class="flex gap-2">
                    <button
                        v-if="wip.status !== 'Completed'"
                        @click="markAsCompleted"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium"
                    >
                        Mark Completed
                    </button>
                    <Link
                        :href="route('wips.edit', wip.id)"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
                    >
                        Edit Job
                    </Link>
                </div>
            </div>
        </template>

        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex gap-8">
                    <button 
                        @click="activeTab = 'details'"
                        class="py-4 text-sm font-medium border-b-2 transition-colors"
                        :class="activeTab === 'details' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    >
                        Job Details
                    </button>
                    <button 
                        @click="activeTab = 'history'"
                        class="py-4 text-sm font-medium border-b-2 transition-colors"
                        :class="activeTab === 'history' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    >
                        Change History
                    </button>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Details -->
                    <div v-if="activeTab === 'details'" class="lg:col-span-2 space-y-8">
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Job Description</h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">
                                {{ wip.description || 'No description provided.' }}
                            </p>
                        </div>

                        <!-- Progress Updates -->
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Progress History</h3>
                                <button
                                    v-if="!showUpdateForm"
                                    @click="showUpdateForm = true"
                                    class="text-sm text-indigo-600 hover:underline font-medium"
                                >
                                    + Add Update
                                </button>
                            </div>

                            <!-- Add Update Form -->
                            <div v-if="showUpdateForm" class="mb-6 p-4 border border-indigo-100 dark:border-indigo-900 bg-indigo-50 dark:bg-indigo-900/10 rounded-lg">
                                <form @submit.prevent="submitUpdate">
                                    <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-300">What did you achieve?</label>
                                    <textarea
                                        v-model="updateForm.content"
                                        class="mt-1 block w-full border-indigo-300 dark:border-indigo-800 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        rows="2"
                                        required
                                        placeholder="Briefly describe your progress..."
                                    ></textarea>
                                    
                                    <div class="mt-4 flex items-center gap-4">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-500 uppercase">Completion: {{ updateForm.percentage_completed }}%</label>
                                            <input
                                                type="range"
                                                v-model="updateForm.percentage_completed"
                                                min="0"
                                                max="100"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600 mt-1"
                                            />
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" @click="showUpdateForm = false" class="text-xs text-gray-500 hover:underline">Cancel</button>
                                            <PrimaryButton class="text-xs py-1 px-3">Post Update</PrimaryButton>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div v-for="update in wip.updates" :key="update.id" class="mb-6 pb-4 border-b dark:border-gray-700 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div class="font-semibold text-indigo-600 dark:text-indigo-400">{{ update.user.name }}</div>
                                    <div class="text-xs text-gray-500">{{ new Date(update.created_at).toLocaleString() }}</div>
                                </div>
                                <div class="mt-2 text-gray-700 dark:text-gray-300">{{ update.content }}</div>
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 max-w-[100px]">
                                        <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${update.percentage_completed}%`"></div>
                                    </div>
                                    <span class="text-xs font-medium">{{ update.percentage_completed }}%</span>
                                </div>
                            </div>
                            <div v-if="wip.updates.length === 0" class="text-gray-500 italic">No progress updates yet.</div>
                        </div>

                        <!-- Blockers -->
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 italic">Blocker Management</h3>
                                <button
                                    v-if="!showBlockerForm"
                                    @click="showBlockerForm = true"
                                    class="text-sm text-red-600 hover:underline font-medium"
                                >
                                    + Raise New Blocker
                                </button>
                            </div>

                            <!-- Raise Blocker Form -->
                            <div v-if="showBlockerForm" class="mb-6 p-4 border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/10 rounded-lg">
                                <form @submit.prevent="submitBlocker">
                                    <label class="block text-sm font-medium text-red-800 dark:text-red-300">Impediment Description</label>
                                    <textarea
                                        v-model="blockerForm.description"
                                        class="mt-1 block w-full border-red-300 dark:border-red-800 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm text-sm"
                                        rows="2"
                                        required
                                        placeholder="What is stopping this job from progressing?"
                                    ></textarea>
                                    
                                    <div class="mt-3 flex items-center gap-4">
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Priority:</label>
                                            <select v-model="blockerForm.priority" class="text-xs border-red-300 dark:border-red-800 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                                <option value="Urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-end gap-2 ml-auto">
                                            <button type="button" @click="showBlockerForm = false" class="text-xs text-gray-500 hover:underline">Cancel</button>
                                            <PrimaryButton class="!bg-red-600 hover:!bg-red-700 text-xs py-1 px-3">Raise Blocker</PrimaryButton>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div v-for="blocker in wip.blockers" :key="blocker.id" class="mb-4 pb-4 border-b dark:border-gray-700 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div :class="blocker.status === 'Open' ? 'text-red-700 dark:text-red-400 font-bold' : 'text-gray-500 line-through'">
                                        <span v-if="blocker.status === 'Open'" 
                                              class="mr-2 px-1.5 py-0.5 rounded text-[10px] uppercase tracking-tighter"
                                              :class="{
                                                  'bg-red-100 text-red-800': blocker.priority === 'Urgent' || blocker.priority === 'High',
                                                  'bg-gray-100 text-gray-700': blocker.priority === 'Low' || blocker.priority === 'Medium'
                                              }">
                                            {{ blocker.priority }}
                                        </span>
                                        {{ blocker.description }}
                                    </div>
                                    <div v-if="blocker.status === 'Open'">
                                        <button
                                            @click="resolveBlocker(blocker.id)"
                                            class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                        >
                                            Resolve
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex justify-between">
                                    <span>Raised by {{ blocker.raised_by.name }}</span>
                                    <span v-if="blocker.status === 'Resolved'" class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded italic">
                                        Resolved in {{ blocker.resolution_duration_string }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="wip.blockers.length === 0" class="text-gray-500 text-sm">No blockers registered for this job.</div>
                        </div>
                    </div>

                    <!-- Left Column: History -->
                    <div v-if="activeTab === 'history'" class="lg:col-span-2 space-y-8">
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Operational Audit Trail</h3>
                            
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li v-for="(log, logIdx) in wip.audit_logs" :key="log.id">
                                        <div class="relative pb-8">
                                            <span v-if="logIdx !== wip.audit_logs.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800" 
                                                          :class="log.event === 'Job Created' ? 'bg-green-500' : 'bg-blue-500'">
                                                        <svg v-if="log.event === 'Job Created'" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                        <svg v-else class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ log.user?.name || 'System' }}</span>
                                                            {{ log.event === 'Job Created' ? 'initialized the job' : 'updated the job details' }}
                                                        </p>
                                                        <div v-if="log.old_values && log.new_values" class="mt-2 text-xs space-y-1">
                                                            <div v-for="(val, key) in log.new_values" :key="key" class="bg-gray-50 dark:bg-gray-900/50 p-2 rounded border dark:border-gray-700">
                                                                <span class="font-bold uppercase text-[10px] text-gray-500">{{ key.replace('_', ' ') }}:</span>
                                                                <span class="text-red-500 line-through ml-2">{{ log.old_values[key] }}</span>
                                                                <span class="text-green-500 ml-2">→ {{ val }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                                        <time>{{ new Date(log.created_at).toLocaleString() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-if="wip.audit_logs.length === 0" class="text-gray-500 italic">No historical logs available for this job.</div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="space-y-8">
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Status & Ownership</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</label>
                                    <div class="mt-1 font-semibold text-lg flex items-center gap-2">
                                        <span
                                            class="h-3 w-3 rounded-full"
                                            :class="{
                                                'bg-green-500': wip.status === 'Active',
                                                'bg-red-500': wip.status === 'Blocked',
                                                'bg-blue-500': wip.status === 'Completed'
                                            }"
                                        ></span>
                                        {{ wip.status }}
                                    </div>
                                </div>
                                <div v-if="wip.is_recurring" class="mt-2">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 text-[10px] font-bold rounded flex items-center gap-1 w-fit">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        Recurring Monthly Job
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</label>
                                    <div class="mt-1 font-semibold">{{ wip.assigned_to?.name || 'Unassigned' }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</label>
                                    <div class="mt-1 font-semibold" :class="new Date(wip.due_date) < new Date() ? 'text-red-600' : ''">
                                        {{ wip.due_date }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Billing Status</label>
                                    <div class="mt-1 font-semibold">{{ wip.billing_status }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Efficiency Stats -->
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Efficiency Metrics</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Impediments Resolved</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ metrics.resolved_count }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mean Time to Resolve</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ metrics.mttr_string || 'N/A' }}</span>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <p class="text-[10px] text-gray-500 italic">
                                        MTTR measures the average time between raising a blocker and its resolution on this specific job.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Blocker Banner (Sidebar) -->
                        <div v-if="wip.blockers.some(b => b.status === 'Open')" class="bg-red-50 dark:bg-red-900/20 shadow sm:rounded-lg p-6 border-l-4 border-red-500">
                            <h3 class="text-lg font-medium text-red-800 dark:text-red-400 flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Active Blockers
                            </h3>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                This job is currently on hold due to active blockers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
