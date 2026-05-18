<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    company: Object,
});

const form = useForm({
    name: props.company.name,
    slug: props.company.slug,
    subscription_plan: props.company.subscription_plan,
    subscription_status: props.company.subscription_status,
});

const userForm = useForm({
    name: '',
    email: '',
    password: 'password',
    role: 'company_admin',
});

const submit = () => {
    form.patch(route('admin.companies.update', props.company.id));
};

const submitUser = () => {
    userForm.post(route('admin.companies.users.store', props.company.id), {
        onSuccess: () => userForm.reset('name', 'email'),
    });
};
</script>

<template>
    <Head :title="`Edit ${company.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Company: {{ company.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="max-w-xl">
                        <div>
                            <InputLabel for="name" value="Company Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="slug" value="Slug" />
                            <TextInput
                                id="slug"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.slug"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.slug" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="subscription_plan" value="Subscription Plan" />
                            <select
                                id="subscription_plan"
                                v-model="form.subscription_plan"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            >
                                <option>Starter</option>
                                <option>Business</option>
                                <option>Professional</option>
                                <option>Enterprise</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.subscription_plan" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="subscription_status" value="Subscription Status" />
                            <select
                                id="subscription_status"
                                v-model="form.subscription_status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            >
                                <option>Trial</option>
                                <option>Active</option>
                                <option>Overdue</option>
                                <option>Suspended</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.subscription_status" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <Link :href="route('admin.companies.index')" class="text-gray-600 dark:text-gray-400 hover:underline text-sm">
                                Back to list
                            </Link>
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Update Company
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- User Management Section -->
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Company Users</h3>
                    
                    <div class="mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in company.users" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ user.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ user.role }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2">Add New User</h4>
                    <form @submit.prevent="submitUser" class="max-w-xl">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="user_name" value="Name" />
                                <TextInput id="user_name" type="text" class="mt-1 block w-full" v-model="userForm.name" required />
                                <InputError class="mt-2" :message="userForm.errors.name" />
                            </div>
                            <div>
                                <InputLabel for="user_email" value="Email" />
                                <TextInput id="user_email" type="email" class="mt-1 block w-full" v-model="userForm.email" required />
                                <InputError class="mt-2" :message="userForm.errors.email" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <InputLabel for="user_password" value="Password" />
                                <TextInput id="user_password" type="password" class="mt-1 block w-full" v-model="userForm.password" required />
                                <InputError class="mt-2" :message="userForm.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="user_role" value="Role" />
                                <select
                                    id="user_role"
                                    v-model="userForm.role"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option value="company_admin">Company Admin</option>
                                    <option value="staff">Staff</option>
                                </select>
                                <InputError class="mt-2" :message="userForm.errors.role" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <PrimaryButton :class="{ 'opacity-25': userForm.processing }" :disabled="userForm.processing">
                                Add User
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
