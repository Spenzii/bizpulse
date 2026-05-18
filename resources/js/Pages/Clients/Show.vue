<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    client: Object,
    portal_users: Array,
});

const showUserForm = ref(false);

const userForm = useForm({
    name: props.client.contact_person || '',
    email: props.client.email || '',
});

const createUser = () => {
    userForm.post(route('clients.portal-users.store', props.client.id), {
        onSuccess: () => {
            userForm.reset();
            showUserForm.value = false;
        }
    });
};
</script>

<template>
    <Head :title="`Client: ${client.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ client.name }} - Portal Management</h2>
                <Link :href="route('clients.index')" class="text-sm text-indigo-600 hover:underline">Back to Register</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Client Info -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Client Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Contact Person</label>
                            <div class="mt-1 font-semibold">{{ client.contact_person || 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Email Address</label>
                            <div class="mt-1 font-semibold">{{ client.email || 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Phone</label>
                            <div class="mt-1 font-semibold">{{ client.phone || 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Portal Users -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Portal Users</h3>
                            <PrimaryButton v-if="!showUserForm" @click="showUserForm = true">
                                Create New Portal User
                            </PrimaryButton>
                        </div>

                        <div v-if="showUserForm" class="mb-8 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h4 class="font-bold mb-4">Create New Portal Access</h4>
                            <form @submit.prevent="createUser" class="space-y-4 max-w-md">
                                <div>
                                    <InputLabel for="u_name" value="User Full Name" />
                                    <TextInput id="u_name" v-model="userForm.name" class="mt-1 block w-full" required />
                                    <InputError :message="userForm.errors.name" />
                                </div>
                                <div>
                                    <InputLabel for="u_email" value="Login Email" />
                                    <TextInput id="u_email" type="email" v-model="userForm.email" class="mt-1 block w-full" required />
                                    <InputError :message="userForm.errors.email" />
                                </div>
                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="userForm.processing">Create User</PrimaryButton>
                                    <button type="button" @click="showUserForm = false" class="text-sm text-gray-500 hover:underline">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in portal_users" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active Portal Access</span>
                                    </td>
                                </tr>
                                <tr v-if="portal_users.length === 0">
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">No portal users created yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Client WIP History -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Work Items (WIP)</h3>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Job Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="wip in client.wips" :key="wip.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ wip.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full" :class="wip.status === 'Completed' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'">
                                            {{ wip.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ wip.due_date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
