<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    slug: '',
    subscription_plan: 'Starter',
    subscription_status: 'Trial',
});

const submit = () => {
    form.post(route('admin.companies.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Add Company" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add New Company</h2>
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
                                autocomplete="organization"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="slug" value="Slug (URL identifier)" />
                            <TextInput
                                id="slug"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.slug"
                                required
                                autocomplete="off"
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

                        <div class="flex items-center justify-end mt-4">
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Company
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
