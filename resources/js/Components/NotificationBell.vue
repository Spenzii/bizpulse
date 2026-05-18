<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const notifications = ref([]);
const unreadCount = ref(0);

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'));
        notifications.value = response.data;
        unreadCount.value = notifications.value.filter(n => n.read_at === null).length;
    } catch (error) {
        console.error('Failed to fetch notifications', error);
    }
};

const markAsRead = (notification) => {
    router.patch(route('notifications.read', notification.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            fetchNotifications();
            if (notification.data.wip_id) {
                router.visit(route('wips.show', notification.data.wip_id));
            }
        }
    });
};

onMounted(() => {
    fetchNotifications();
    // Optional: Poll every 30 seconds
    setInterval(fetchNotifications, 30000);
});
</script>

<template>
    <div class="relative ms-3">
        <Dropdown align="right" width="80">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 relative"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            {{ unreadCount }}
                        </span>
                    </button>
                </span>
            </template>

            <template #content>
                <div class="p-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b dark:border-gray-700">
                    Notifications
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <div v-for="notification in notifications" :key="notification.id" 
                         @click="markAsRead(notification)"
                         class="p-4 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                         :class="{'bg-indigo-50 dark:bg-indigo-900/10': !notification.read_at}"
                    >
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ notification.data.message }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ new Date(notification.created_at).toLocaleString() }}
                        </div>
                    </div>
                    <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                        No notifications yet.
                    </div>
                </div>
            </template>
        </Dropdown>
    </div>
</template>
