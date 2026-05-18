<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    plans: Array,
});

const showModal = ref(false);
const form = useForm({
    id: null,
    name: '',
    monthly_fee: 0,
    setup_fee: 0,
    training_fee: 0,
    user_limit: 0,
    features: '',
    is_recommended: false,
});

const editPlan = (plan) => {
    form.id = plan.id;
    form.name = plan.name;
    form.monthly_fee = plan.monthly_fee;
    form.setup_fee = plan.setup_fee;
    form.training_fee = plan.training_fee;
    form.user_limit = plan.user_limit;
    form.features = Array.isArray(plan.features) ? plan.features.join('\n') : plan.features;
    form.is_recommended = plan.is_recommended;
    showModal.value = true;
};

const savePlan = () => {
    const data = { ...form.data(), features: form.features.split('\n').filter(f => f.trim() !== '') };
    if (form.id) {
        form.patch(route('admin.subscription-plans.update', form.id), {
            onSuccess: () => showModal.value = false,
        });
    } else {
        form.post(route('admin.subscription-plans.store'), {
            onSuccess: () => showModal.value = false,
        });
    }
};

const formatCurrency = (val) => new Intl.NumberFormat('en-PG', { style: 'currency', currency: 'PGK' }).format(val);
</script>

<template>
    <Head title="Subscription Plans" />

    <AdminLayout>
        <template #header>Manage Subscription Plans</template>

        <div class="mb-6 flex justify-end">
            <button @click="form.reset(); form.id = null; showModal = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-md transition-all">
                + Create New Plan
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="plan in plans" :key="plan.id" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ plan.name }}</h3>
                        <span v-if="plan.is_recommended" class="text-[10px] bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-widest">Recommended</span>
                    </div>
                    <button @click="editPlan(plan)" class="text-gray-400 hover:text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </button>
                </div>
                <div class="p-6 flex-1 space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-gray-500 text-sm">Monthly Fee</span>
                        <span class="text-2xl font-black text-indigo-600">{{ formatCurrency(plan.monthly_fee) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-50">
                        <div>
                            <span class="block text-[10px] text-gray-400 uppercase font-bold">Setup Fee</span>
                            <span class="text-sm font-bold">{{ formatCurrency(plan.setup_fee) }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 uppercase font-bold">Training Fee</span>
                            <span class="text-sm font-bold">{{ formatCurrency(plan.training_fee) }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold mb-2">Features Included</span>
                        <ul class="text-sm space-y-2">
                            <li v-for="feature in plan.features" :key="feature" class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" /></svg>
                                {{ feature }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plan Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold">{{ form.id ? 'Edit Plan' : 'Create New Plan' }}</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Plan Name</label>
                        <input type="text" v-model="form.name" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 shadow-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Monthly Fee</label>
                            <input type="number" v-model="form.monthly_fee" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Setup Fee</label>
                            <input type="number" v-model="form.setup_fee" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 shadow-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Features (one per line)</label>
                        <textarea v-model="form.features" rows="5" class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.is_recommended" id="is_rec" class="rounded text-indigo-600" />
                        <label for="is_rec" class="text-sm font-medium">Recommend this plan</label>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                    <button @click="savePlan" class="px-6 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Save Plan</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
