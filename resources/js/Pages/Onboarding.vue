<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    company: Object,
    user: Object,
});

const currentStep = ref(1);

const form = useForm({
    phone: '',
    country: 'Papua New Guinea',
    city: 'Port Moresby',
    address_line_1: '',
    tax_id: '',
    industry_type: 'General SME',
    staff: [
        { name: '', email: '' }
    ],
    client_name: '',
    client_sector: '',
    wip_name: '',
    wip_priority: 'medium',
    wip_estimated_fee: '',
    wip_due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 7 days from now
    wip_next_action: '',
});

const industries = [
    {
        name: 'General SME',
        desc: 'Best for standard operations, retail, and general services.',
        tasks: ['Complete Business Registration Extract', 'Quote preparation for new prospect']
    },
    {
        name: 'Accounting Firm',
        desc: 'Best for bookkeepers, accountants, and finance professionals.',
        tasks: ['Monthly GST Filing', 'Annual Income Tax Return 2025']
    },
    {
        name: 'Construction Company',
        desc: 'Best for builders, engineers, and sub-contracting operations.',
        tasks: ['Initial Site Inspection & Safety Check', 'Subcontractor Agreement Review']
    },
    {
        name: 'ICT Support Company',
        desc: 'Best for technology consultants, web agencies, and helpdesks.',
        tasks: ['Server & Email Infrastructure Setup', 'Website Content Management Updates']
    },
    {
        name: 'Legal Services',
        desc: 'Best for law offices, legal practitioners, and corporate advisors.',
        tasks: ['Draft Shareholder Engagement Contract', 'Client Legal Consultation Meeting']
    },
    {
        name: 'Security Services',
        desc: 'Best for security agencies, guard deployments, and patrol teams.',
        tasks: ['Patrol Route Configuration Plan', 'Staff Deployment Briefing']
    },
    {
        name: 'NGO / Project Team',
        desc: 'Best for associations, non-profits, and international aid agencies.',
        tasks: ['Quarterly Stakeholder Progress Report', 'Grant Proposal Submission']
    },
    {
        name: 'Logistics / Field Operations',
        desc: 'Best for freight teams, fleet handlers, and warehouse logistics.',
        tasks: ['Fleet Vehicle Safety Maintenance Review', 'Warehouse Inventory Reconciliation Audit']
    }
];

const selectedIndustryInfo = computed(() => {
    return industries.find(ind => ind.name === form.industry_type) || industries[0];
});

const addStaffRow = () => {
    form.staff.push({ name: '', email: '' });
};

const removeStaffRow = (index) => {
    form.staff.splice(index, 1);
};

const nextStep = () => {
    // Basic step validation
    if (currentStep.value === 1) {
        if (!form.country || !form.city) return;
    } else if (currentStep.value === 2) {
        if (!form.industry_type) return;
        form.client_sector = form.industry_type;
    } else if (currentStep.value === 4) {
        if (!form.client_name) return;
    } else if (currentStep.value === 5) {
        if (!form.wip_name || !form.wip_due_date || !form.wip_next_action) return;
    }
    currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submitOnboarding = () => {
    form.post(route('onboarding.store'));
};
</script>

<template>
    <Head title="Welcome Setup Wizard" />

    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between font-sans selection:bg-teal-500 selection:text-slate-950 relative overflow-hidden">
        <!-- Background Orbs -->
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-teal-500/10 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-indigo-500/10 blur-[130px] pointer-events-none"></div>

        <!-- Header -->
        <header class="border-b border-slate-800/80 bg-slate-950/70 backdrop-blur-md px-6 py-4 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <span class="text-xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">BizPulse</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-slate-400">Tenant Setup</span>
            </div>
            <div class="text-sm text-slate-400">
                Logged in as <strong class="text-slate-200">{{ user.name }}</strong>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 flex items-center justify-center p-6 z-10">
            <div class="w-full max-w-3xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl rounded-2xl p-8 shadow-2xl relative">
                
                <!-- Progress Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center text-xs font-semibold text-slate-400 uppercase tracking-widest mb-3">
                        <span>Step {{ currentStep }} of 6</span>
                        <span class="text-teal-400 font-bold">{{ Math.round((currentStep / 6) * 100) }}% Complete</span>
                    </div>
                    <!-- Indicator Bar -->
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 transition-all duration-500" :style="{ width: `${(currentStep / 6) * 100}%` }"></div>
                    </div>
                </div>

                <!-- Forms Area -->
                <div>
                    <!-- Step 1: Company Profile -->
                    <div v-if="currentStep === 1">
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">Tell us about <span class="text-teal-400 font-extrabold">{{ company.name }}</span></h2>
                        <p class="text-slate-400 text-sm mb-6">Let's set up your business details. This information will appear on your professional client invoices.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Country</label>
                                <select v-model="form.country" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200">
                                    <option>Papua New Guinea</option>
                                    <option>Fiji</option>
                                    <option>Solomon Islands</option>
                                    <option>Vanuatu</option>
                                    <option>Samoa</option>
                                    <option>Tonga</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">City / Location</label>
                                <input type="text" v-model="form.city" placeholder="e.g. Port Moresby" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" required />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Physical Office Address</label>
                                <input type="text" v-model="form.address_line_1" placeholder="e.g. Level 2, Pacific Place, Town" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Phone Contact</label>
                                <input type="text" v-model="form.phone" placeholder="e.g. +675 321 0000" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Tax ID / TIN (Optional)</label>
                                <input type="text" v-model="form.tax_id" placeholder="e.g. 500123456" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Select Template -->
                    <div v-if="currentStep === 2">
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">Select your industry workspace</h2>
                        <p class="text-slate-400 text-sm mb-6">We will customize your workflow and seed templates tailored specifically for your sector.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[300px] overflow-y-auto pr-2 border border-slate-800/50 p-2 rounded-lg bg-slate-950/40">
                            <div 
                                v-for="ind in industries" 
                                :key="ind.name" 
                                @click="form.industry_type = ind.name"
                                class="p-4 rounded-xl border transition-all cursor-pointer text-left"
                                :class="form.industry_type === ind.name ? 'bg-teal-950/20 border-teal-500/80 text-slate-100 shadow-[0_0_15px_rgba(20,184,166,0.1)]' : 'bg-slate-900 border-slate-800 hover:border-slate-700 text-slate-400'"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-sm" :class="form.industry_type === ind.name ? 'text-teal-400' : 'text-slate-200'">{{ ind.name }}</span>
                                    <span v-if="form.industry_type === ind.name" class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>
                                </div>
                                <p class="text-xs leading-relaxed text-slate-400">{{ ind.desc }}</p>
                            </div>
                        </div>

                        <!-- Seeding Task Preview -->
                        <div class="mt-5 p-4 rounded-lg bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                            <div class="text-left">
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Suggested items to seed:</span>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="task in selectedIndustryInfo.tasks" :key="task" class="text-xs px-2.5 py-1 rounded-md bg-slate-800 border border-slate-700 text-slate-300">
                                        📁 {{ task }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs text-teal-500 font-bold bg-teal-950/30 border border-teal-900/50 px-2 py-0.5 rounded">+ Auto-Seeded</span>
                        </div>
                    </div>

                    <!-- Step 3: Invite Staff -->
                    <div v-if="currentStep === 3">
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">Assemble your team</h2>
                        <p class="text-slate-400 text-sm mb-6">Invite your managing partners, accountants, supervisors, or field operators to start assigning WIP tasks.</p>

                        <div class="max-h-[250px] overflow-y-auto pr-2 mb-4 space-y-3">
                            <div v-for="(row, idx) in form.staff" :key="idx" class="flex gap-3 items-center">
                                <input type="text" v-model="row.name" placeholder="Full Name (e.g.Rosinta Paul)" class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                                <input type="email" v-model="row.email" placeholder="Email (e.g. rosinta@firm.com)" class="flex-1 bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                                <button type="button" @click="removeStaffRow(idx)" class="p-2.5 rounded-lg bg-slate-950 hover:bg-red-950/20 text-slate-500 hover:text-red-400 border border-slate-800 transition" :disabled="form.staff.length === 1">
                                    🗑
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="addStaffRow" class="text-sm font-bold text-teal-400 hover:text-teal-300 transition flex items-center gap-1">
                            ➕ Add Another Team Member
                        </button>
                    </div>

                    <!-- Step 4: Add First Client -->
                    <div v-if="currentStep === 4">
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">Log your first client</h2>
                        <p class="text-slate-400 text-sm mb-6">Every job or task in BizPulse is owned by a client. Let's create your first client company contact record.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-left">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Client Company / Customer Name</label>
                                <input type="text" v-model="form.client_name" placeholder="e.g. PNG Football Association" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Industry Sector</label>
                                <input type="text" v-model="form.client_sector" placeholder="e.g. Sports Management, Logistics, Retail" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Client Relationship Status</label>
                                <span class="block px-4 py-3 rounded-lg bg-slate-950/80 border border-slate-800 text-sm text-teal-400 font-semibold">Active Client</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Add First WIP / Job -->
                    <div v-if="currentStep === 5">
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">Create your first Job (WIP)</h2>
                        <p class="text-slate-400 text-sm mb-6">Convert your active client's instruction into an official WIP job trackable by your firm.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-left">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Job / WIP Title</label>
                                <input type="text" v-model="form.wip_name" placeholder="e.g. Engagement Letter & GST Setup" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Target Completion Date</label>
                                <input type="date" v-model="form.wip_due_date" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">WIP Priority</label>
                                <select v-model="form.wip_priority" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200">
                                    <option value="critical">🔴 Critical</option>
                                    <option value="high">🟠 High</option>
                                    <option value="medium">🟡 Medium</option>
                                    <option value="low">🟢 Low</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Next Planned Action</label>
                                <input type="text" v-model="form.wip_next_action" placeholder="e.g. Finalize GST filing form and dispatch to Managing Partner" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Estimated Setup Fee (PGK Kina - Optional)</label>
                                <input type="number" v-model="form.wip_estimated_fee" placeholder="e.g. 4500" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-3 focus:outline-none focus:border-teal-500 text-slate-200" />
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Review & Success -->
                    <div v-if="currentStep === 6" class="text-center py-4">
                        <div class="w-16 h-16 bg-teal-500/10 border border-teal-500/50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl text-teal-400">
                            🚀
                        </div>
                        <h2 class="text-2xl font-bold mb-2 text-slate-100">You are ready to launch!</h2>
                        <p class="text-slate-400 text-sm mb-6">Review your setup configuration before launching your official BizPulse workspace.</p>

                        <div class="grid grid-cols-2 gap-4 text-left bg-slate-950/60 border border-slate-800 p-5 rounded-xl text-sm mb-6">
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">Business Name</span>
                                <strong class="text-slate-200">{{ company.name }}</strong>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">Industry Workspace</span>
                                <strong class="text-slate-200">{{ form.industry_type }}</strong>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">Staff Invited</span>
                                <strong class="text-slate-200">{{ form.staff.filter(s => s.name && s.email).length }} team member(s)</strong>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">First Client Record</span>
                                <strong class="text-slate-200">{{ form.client_name }}</strong>
                            </div>
                            <div class="col-span-2 border-t border-slate-800/80 pt-3 mt-1">
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-500">First Work In Progress (WIP)</span>
                                <strong class="text-slate-200">💼 {{ form.wip_name }}</strong>
                                <span class="block text-xs text-slate-400 mt-1">Target date: {{ form.wip_due_date }} | Next Action: {{ form.wip_next_action }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 mb-6">By completing, the system will apply templates, seed audit logs, configure security permissions, and prepare your dashboard.</p>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <div class="mt-8 border-t border-slate-800/80 pt-6 flex justify-between">
                    <button 
                        type="button" 
                        @click="prevStep" 
                        class="px-6 py-2.5 rounded-lg border border-slate-800 hover:border-slate-700 bg-slate-950 text-slate-300 font-semibold hover:text-slate-100 transition"
                        :class="currentStep === 1 ? 'opacity-0 pointer-events-none' : ''"
                    >
                        Previous
                    </button>

                    <button 
                        v-if="currentStep < 6"
                        type="button" 
                        @click="nextStep" 
                        class="px-8 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-slate-950 font-bold hover:shadow-[0_0_20px_rgba(20,184,166,0.2)] transition"
                    >
                        Next Step
                    </button>

                    <button 
                        v-else
                        type="button" 
                        @click="submitOnboarding" 
                        class="px-8 py-2.5 rounded-lg bg-gradient-to-r from-teal-500 to-emerald-400 text-slate-950 font-black hover:shadow-[0_0_20px_rgba(20,184,166,0.3)] transition"
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                    >
                        🚀 Launch Workspace
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/50 py-6 text-center text-xs text-slate-500 z-10">
            &copy; 2026 BizPulse SaaS Platform. Professional Client, Job, WIP, and Billing rhythm built for Pacific SMEs.
        </footer>
    </div>
</template>
