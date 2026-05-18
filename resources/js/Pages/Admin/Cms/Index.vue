<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sections: Array,
    seo: Array,
    media_count: Number,
});

const activeTab = ref('sections');

const tabs = [
    { id: 'sections', name: 'Website Sections', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
    { id: 'seo', name: 'SEO Settings', icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' },
    { id: 'media', name: 'Media Library', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
];

const selectedSection = ref(null);
const sectionForm = useForm({
    content: {},
    is_active: true,
});

const editSection = (section) => {
    selectedSection.value = section;
    sectionForm.content = { ...section.content };
    sectionForm.is_active = section.is_active;
};

const saveSection = () => {
    sectionForm.patch(route('admin.cms.sections.update', selectedSection.value.id), {
        onSuccess: () => selectedSection.value = null,
    });
};
</script>

<template>
    <Head title="Website CMS" />

    <AdminLayout>
        <template #header>Website Content Management</template>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden text-gray-800 dark:text-gray-100">
            <!-- Tabs Header -->
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="flex items-center gap-2 px-6 py-4 text-sm font-medium transition-colors border-b-2"
                    :class="activeTab === tab.id ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" />
                    </svg>
                    {{ tab.name }}
                </button>
            </div>

            <div class="p-8">
                <!-- Sections Tab -->
                <div v-if="activeTab === 'sections'" class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">Manage Website Sections</h3>
                        <p class="text-sm text-gray-500">Enable, disable, and edit the content of your public landing page.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div 
                            v-for="section in sections" 
                            :key="section.id"
                            class="p-5 border rounded-xl border-gray-200 hover:border-indigo-300 transition-all cursor-pointer group"
                            @click="editSection(section)"
                        >
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 bg-gray-100 text-[10px] font-bold uppercase tracking-wider rounded">{{ section.key }}</span>
                                <span :class="section.is_active ? 'text-green-500' : 'text-gray-400'">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>
                                </span>
                            </div>
                            <h4 class="text-lg font-bold group-hover:text-indigo-600">{{ section.name }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Click to edit section content and settings.</p>
                        </div>
                    </div>
                </div>

                <!-- SEO Tab -->
                <div v-if="activeTab === 'seo'" class="space-y-6">
                    <h3 class="text-xl font-bold">SEO & Meta Settings</h3>
                    <div class="text-sm text-gray-500">Configure how your site appears in search engines and social media.</div>
                    <!-- SEO Form would go here -->
                </div>

                <!-- Media Tab -->
                <div v-if="activeTab === 'media'" class="space-y-6">
                    <h3 class="text-xl font-bold">Media Library</h3>
                    <div class="text-sm text-gray-500">Upload and manage images, icons, and backgrounds for your website.</div>
                    <div class="mt-8 border-2 border-dashed border-gray-200 rounded-xl p-12 text-center text-gray-400">
                        <svg class="h-12 w-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h14a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <p>No media files uploaded yet.</p>
                        <button class="mt-4 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Upload Files</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Editor Modal -->
        <div v-if="selectedSection" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold">Edit {{ selectedSection.name }}</h3>
                    <button @click="selectedSection = null" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                    <div v-for="(value, key) in sectionForm.content" :key="key">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">{{ key.replace('_', ' ') }}</label>
                        <input 
                            v-if="typeof value === 'string' && value.length < 100" 
                            type="text" 
                            v-model="sectionForm.content[key]"
                            class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <textarea 
                            v-else-if="typeof value === 'string'"
                            v-model="sectionForm.content[key]"
                            rows="4"
                            class="w-full border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" v-model="sectionForm.is_active" id="is_active" class="rounded text-indigo-600" />
                        <label for="is_active" class="text-sm font-medium">This section is active on the website</label>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 flex justify-end gap-3">
                    <button @click="selectedSection = null" class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</button>
                    <button @click="saveSection" class="px-6 py-2 text-sm font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Save Changes</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
