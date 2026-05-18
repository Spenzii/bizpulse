<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    wips: Array,
    users: Array,
    filters: Object,
});

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const selectedUserId = ref(props.filters.assigned_to_id || '');

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

const daysInMonth = (month, year) => new Date(year, month + 1, 0).getDate();
const firstDayOfMonth = (month, year) => new Date(year, month, 1).getDay();

const calendarDays = computed(() => {
    const days = [];
    const totalDays = daysInMonth(currentMonth.value, currentYear.value);
    const firstDay = firstDayOfMonth(currentMonth.value, currentYear.value);

    // Padding for previous month
    for (let i = 0; i < firstDay; i++) {
        days.push({ day: null, date: null });
    }

    // Actual days
    for (let d = 1; d <= totalDays; d++) {
        const dateStr = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const dayWips = props.wips.filter(wip => wip.due_date === dateStr);
        days.push({
            day: d,
            date: dateStr,
            wips: dayWips,
            isToday: new Date().toDateString() === new Date(currentYear.value, currentMonth.value, d).toDateString()
        });
    }

    return days;
});

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const filterByUser = () => {
    router.get(route('planner.index'), { assigned_to_id: selectedUserId.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Resource Planner" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Resource Planner
                </h2>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">View Scope:</label>
                        <select 
                            v-model="selectedUserId" 
                            @change="filterByUser"
                            class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">All Staff</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                    <!-- Calendar Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ monthNames[currentMonth] }} {{ currentYear }}
                        </h3>
                        <div class="flex gap-2">
                            <button @click="prevMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors text-gray-600 dark:text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button @click="prevMonth = null; currentMonth = new Date().getMonth(); currentYear = new Date().getFullYear();" class="px-4 py-2 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors text-gray-600 dark:text-gray-400">
                                Today
                            </button>
                            <button @click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors text-gray-600 dark:text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-900 text-center border-b border-gray-200 dark:border-gray-700">
                        <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="py-3 text-xs font-semibold text-gray-500 uppercase tracking-widest">
                            {{ day }}
                        </div>
                    </div>

                    <div class="grid grid-cols-7 border-l border-t border-gray-200 dark:border-gray-700">
                        <div 
                            v-for="(dateObj, index) in calendarDays" 
                            :key="index"
                            class="min-h-[140px] border-r border-b border-gray-200 dark:border-gray-700 p-2 relative group transition-colors"
                            :class="[
                                dateObj.day ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/50 dark:bg-gray-900/50',
                                dateObj.isToday ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : ''
                            ]"
                        >
                            <span 
                                v-if="dateObj.day" 
                                class="text-sm font-medium"
                                :class="dateObj.isToday ? 'bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-full' : 'text-gray-500 dark:text-gray-400'"
                            >
                                {{ dateObj.day }}
                            </span>

                            <!-- WIP Event Items -->
                            <div v-if="dateObj.wips?.length" class="mt-2 space-y-1">
                                <Link
                                    v-for="wip in dateObj.wips"
                                    :key="wip.id"
                                    :href="route('wips.show', wip.id)"
                                    class="block p-1 text-[10px] rounded border leading-tight transition-all hover:scale-[1.02]"
                                    :class="{
                                        'bg-green-100 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300': wip.status === 'Active',
                                        'bg-red-100 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300': wip.status === 'Blocked',
                                        'bg-blue-100 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300': wip.status === 'Completed'
                                    }"
                                >
                                    <div class="font-bold truncate">{{ wip.client.name }}</div>
                                    <div class="truncate">{{ wip.name }}</div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.grid-cols-7 {
    grid-template-columns: repeat(7, minmax(0, 1fr));
}
</style>
