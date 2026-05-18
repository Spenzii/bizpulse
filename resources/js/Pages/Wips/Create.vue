<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    clients: Array,
    users: Array,
});

const form = useForm({
    client_id: '',
    name: '',
    description: '',
    estimated_fee: '',
    due_date: '',
    assigned_to_id: '',
    status: 'Active',
    is_recurring: false,
});

const submit = () => {
    form.post(route('wips.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Add Work Item" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add New Work Item</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="max-w-xl">
                        <div>
                            <InputLabel for="client_id" value="Client" />
                            <select
                                id="client_id"
                                v-model="form.client_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Select a client</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }}
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.client_id" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="name" value="Job Name" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                placeholder="e.g. Annual Audit 2025"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="description" value="Description (Optional)" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="due_date" value="Due Date" />
                                <TextInput
                                    id="due_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.due_date"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>
                            <div>
                                <InputLabel for="estimated_fee" value="Estimated Fee (PGK / $)" />
                                <TextInput
                                    id="estimated_fee"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.estimated_fee"
                                    placeholder="0.00"
                                />
                                <InputError class="mt-2" :message="form.errors.estimated_fee" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="status" value="Initial Status" />
                            <select
                                id="status"
                                v-model="form.status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            >
                                <option>Active</option>
                                <option>Blocked</option>
                                <option>Completed</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_recurring"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-medium italic">Recurring Monthly Job</span>
                            </label>
                            <p class="text-[10px] text-gray-500 mt-1 ml-6">
                                Flagging as recurring allows for quick "roll-over" to the next month using bulk actions.
                            </p>
                        </div>

                        <div class="mt-4">
                            <InputLabel for="assigned_to_id" value="Assign To (Optional)" />
                            <select
                                id="assigned_to_id"
                                v-model="form.assigned_to_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Unassigned</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }} ({{ user.role }})
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.assigned_to_id" />
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <Link :href="route('wips.index')" class="text-gray-600 dark:text-gray-400 hover:underline text-sm">
                                Cancel
                            </Link>
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Work Item
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
