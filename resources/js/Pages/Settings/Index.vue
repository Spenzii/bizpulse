<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    company: Object,
});

const logoPreview = ref(props.company.logo_path ? `/storage/${props.company.logo_path}` : null);

const form = useForm({
    name: props.company.name,
    primary_color: props.company.primary_color || '#4f46e5',
    secondary_color: props.company.secondary_color || '#1e293b',
    address_line_1: props.company.address_line_1 || '',
    address_line_2: props.company.address_line_2 || '',
    tax_id: props.company.tax_id || '',
    logo: null,
});

const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('settings.update'), {
        forceFormData: true,
        onSuccess: () => {
            // Optional: show toast
        }
    });
};
</script>

<template>
    <Head title="Firm Settings & Branding" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Firm Settings & Branding</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Branding Preview Sidebar -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Live Branding Preview</h3>
                            
                            <div class="space-y-6">
                                <!-- Logo Preview -->
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Firm Logo</div>
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-8 flex items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-700 aspect-video overflow-hidden">
                                        <img v-if="logoPreview" :src="logoPreview" alt="Logo Preview" class="max-h-full object-contain" />
                                        <div v-else class="text-gray-400 text-center">
                                            <svg class="h-12 w-12 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span class="text-xs italic">No logo uploaded</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Color Preview -->
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Color Palette</div>
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <div class="h-12 rounded-xl shadow-inner border border-white/10" :style="{ backgroundColor: form.primary_color }"></div>
                                            <div class="text-[10px] font-bold text-center mt-2 uppercase text-gray-500">Primary</div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="h-12 rounded-xl shadow-inner border border-white/10" :style="{ backgroundColor: form.secondary_color }"></div>
                                            <div class="text-[10px] font-bold text-center mt-2 uppercase text-gray-500">Secondary</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sample Card -->
                                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <div class="p-4 rounded-xl text-white shadow-lg transition-all duration-500" :style="{ backgroundColor: form.primary_color }">
                                        <div class="text-[10px] font-black uppercase tracking-widest opacity-70">Sample Component</div>
                                        <div class="text-lg font-black mt-1">Premium Interface</div>
                                        <button class="mt-3 px-3 py-1 bg-white/20 rounded-lg text-xs font-bold hover:bg-white/30 transition-all">Action Button</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-indigo-600 p-6 rounded-2xl shadow-xl text-white">
                            <h4 class="font-black text-lg mb-2">Why Branding Matters?</h4>
                            <p class="text-sm text-indigo-100 opacity-80 leading-relaxed">
                                A consistent brand builds trust. Your logo and colors will be applied to the **Client Portal**, **Invoices**, and **Automated Emails** to ensure a premium professional experience.
                            </p>
                        </div>
                    </div>

                    <!-- Settings Form -->
                    <div class="md:col-span-2">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-8 border-b border-gray-50 dark:border-gray-700 pb-4 flex items-center gap-3">
                                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    Firm Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Firm Registered Name</label>
                                        <input v-model="form.name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-bold" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Primary Brand Color</label>
                                        <div class="flex gap-2">
                                            <input v-model="form.primary_color" type="color" class="h-10 w-12 border-none bg-transparent cursor-pointer" />
                                            <input v-model="form.primary_color" type="text" class="flex-1 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-mono text-sm" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Secondary Accent Color</label>
                                        <div class="flex gap-2">
                                            <input v-model="form.secondary_color" type="color" class="h-10 w-12 border-none bg-transparent cursor-pointer" />
                                            <input v-model="form.secondary_color" type="text" class="flex-1 bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-mono text-sm" />
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Upload New Logo</label>
                                        <input type="file" @change="handleLogoUpload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Details -->
                            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-8 border-b border-gray-50 dark:border-gray-700 pb-4 flex items-center gap-3">
                                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    Invoice Header Details
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Address Line 1</label>
                                        <input v-model="form.address_line_1" type="text" placeholder="e.g., Level 5, Pacific Place" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-medium" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Address Line 2 (City, Country)</label>
                                        <input v-model="form.address_line_2" type="text" placeholder="e.g., Port Moresby, Papua New Guinea" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-medium" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Tax ID / TIN</label>
                                        <input v-model="form.tax_id" type="text" placeholder="e.g., 500-123-456" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-bold" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all active:scale-95 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Saving Changes...' : 'Save Firm Settings' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
