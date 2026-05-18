<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const activeTab = ref('features');

const contactForm = ref({
    name: '',
    email: '',
    company: '',
    phone: '',
    message: '',
    submitted: false
});

const submitContact = () => {
    // Mock submit for landing page
    contactForm.value.submitted = true;
};
</script>

<template>
    <Head title="No More Lost Instructions - SME WIP & Billing Platform" />

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-teal-500 selection:text-slate-950 relative overflow-x-hidden">
        <!-- Floating Blur Orbs -->
        <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-teal-500/10 blur-[130px] pointer-events-none"></div>
        <div class="absolute top-[40%] left-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-500/5 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-indigo-500/5 blur-[140px] pointer-events-none"></div>

        <!-- Sticky Header / Navbar -->
        <header class="sticky top-0 bg-slate-950/80 backdrop-blur-lg border-b border-slate-800/60 py-4 px-6 md:px-12 flex items-center justify-between z-50">
            <div class="flex items-center gap-3">
                <ApplicationLogo class="h-8 w-auto" variant="white" />
                <span class="hidden md:inline text-xs border border-slate-800 px-2 py-0.5 rounded bg-slate-900 text-slate-400 font-medium">Built for Pacific SMEs</span>
            </div>
            
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-400">
                <a href="#features" class="hover:text-teal-400 transition">Features</a>
                <a href="#workflow" class="hover:text-teal-400 transition">Workflow</a>
                <a href="#case-study" class="hover:text-teal-400 transition">Case Study</a>
                <a href="#pricing" class="hover:text-teal-400 transition">Pricing</a>
                <a href="#contact" class="hover:text-teal-400 transition">Enquiries</a>
            </nav>

            <div class="flex items-center gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-bold text-slate-300 hover:text-slate-100 transition">
                    Go to Workspace
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="text-sm font-bold text-slate-300 hover:text-slate-100 transition">
                        Log in
                    </Link>
                    <Link :href="route('register')" class="hidden sm:inline-flex px-5 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-slate-950 font-bold hover:shadow-[0_0_15px_rgba(20,184,166,0.2)] transition text-sm">
                        Start Trial
                    </Link>
                </template>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 md:py-32 px-6 md:px-12 max-w-7xl mx-auto text-center z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-950/30 border border-teal-900/50 text-xs text-teal-400 font-bold mb-6 tracking-wide uppercase">
                ⚡ Release v1.1: White-labeled Billing & Custom SVG Charts
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-tight text-transparent bg-clip-text bg-gradient-to-b from-slate-50 to-slate-200 mb-6 max-w-4xl mx-auto">
                Stop losing business instructions in <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400">WhatsApp</span> and text messages.
            </h1>
            <p class="text-slate-400 text-base md:text-lg max-w-3xl mx-auto leading-relaxed mb-10">
                BizPulse helps PNG and Pacific SMEs track clients, jobs, staff tasks, blockers, billing readiness, weekly reviews, and management dashboards in one simple system.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link :href="route('register')" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-400 text-slate-950 font-black hover:shadow-[0_0_25px_rgba(20,184,166,0.3)] transition text-center shadow-lg">
                    Start Your 14-Day Free Trial
                </Link>
                <Link :href="route('login')" class="w-full sm:w-auto px-8 py-3.5 rounded-xl border border-slate-800 bg-slate-950 text-slate-300 font-semibold hover:border-slate-700 hover:text-slate-100 transition text-center">
                    View Interactive Demo
                </Link>
            </div>

            <!-- Dashboard Preview Block (Glassmorphic Mockup) -->
            <div class="mt-16 border border-slate-800/80 rounded-2xl p-2 bg-slate-900/20 backdrop-blur-xl max-w-5xl mx-auto shadow-2xl relative">
                <div class="border border-slate-800 rounded-xl overflow-hidden bg-slate-950 relative">
                    <!-- Top Bar of mockup -->
                    <div class="border-b border-slate-800 bg-slate-950 px-4 py-3 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
                        <span class="text-xs text-slate-500 font-mono ml-4">bizpulse-app.smartbiz.com/dashboard</span>
                    </div>
                    <!-- Mockup Analytics Content -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-950/60 text-left">
                        <div class="md:col-span-3 grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg">
                                <span class="block text-xs font-bold text-slate-500 uppercase">Active WIP Jobs</span>
                                <strong class="text-2xl text-teal-400">38 Jobs</strong>
                                <span class="block text-xs text-slate-400 mt-1">60% Average Progress</span>
                            </div>
                            <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg">
                                <span class="block text-xs font-bold text-slate-500 uppercase">Overdue Tasks</span>
                                <strong class="text-2xl text-red-400">7 Overdue</strong>
                                <span class="block text-xs text-slate-400 mt-1">Urgent Action Required</span>
                            </div>
                            <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg">
                                <span class="block text-xs font-bold text-slate-500 uppercase">Ready to Invoice</span>
                                <strong class="text-2xl text-amber-400">K28,400</strong>
                                <span class="block text-xs text-slate-400 mt-1">Pending Invoices</span>
                            </div>
                            <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg">
                                <span class="block text-xs font-bold text-slate-500 uppercase">Revenue at Risk</span>
                                <strong class="text-2xl text-orange-400">K41,750</strong>
                                <span class="block text-xs text-slate-400 mt-1">Open Blockers Logging</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problem Statement -->
        <section class="py-20 bg-slate-900/40 border-y border-slate-900 px-6 z-10 relative">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-black mb-4">The problem with managing work through WhatsApp</h2>
                <p class="text-slate-400 text-sm md:text-base max-w-2xl mx-auto mb-12">
                    WhatsApp is great for messaging, but it is a terrible workspace for managing an operations and billing pipeline.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-xl border border-slate-800/80 bg-slate-950 text-left">
                        <span class="text-2xl mb-4 block">📥</span>
                        <h3 class="text-lg font-bold mb-2 text-slate-200">Buried Instructions</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Instructions and files get buried under hundreds of chat messages, causing missing scope and missed deadlines.
                        </p>
                    </div>
                    <div class="p-6 rounded-xl border border-slate-800/80 bg-slate-950 text-left">
                        <span class="text-2xl mb-4 block">🤷‍♂️</span>
                        <h3 class="text-lg font-bold mb-2 text-slate-200">No Real Accountability</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            No clear owner or due date for each specific instruction, making it impossible to audit who is currently holding a job.
                        </p>
                    </div>
                    <div class="p-6 rounded-xl border border-slate-800/80 bg-slate-950 text-left">
                        <span class="text-2xl mb-4 block">💸</span>
                        <h3 class="text-lg font-bold mb-2 text-slate-200">Unbilled Completed Work</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Managers cannot see when jobs are completed, resulting in unbilled tasks that leave massive revenue behind.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Workflow Showcase -->
        <section id="workflow" class="py-20 max-w-6xl mx-auto px-6 z-10 relative">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-black mb-4">One Tool. One Truth.</h2>
                <p class="text-slate-400 text-sm max-w-2xl mx-auto">
                    BizPulse transforms every single instruction into an official, auditable workflow record with a clear path.
                </p>
            </div>

            <!-- Visual Workflow Graphics using CSS Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 items-center">
                <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40 relative">
                    <span class="block text-xs font-bold text-teal-400 uppercase tracking-widest mb-1">01</span>
                    <strong class="text-sm text-slate-200 block">Client</strong>
                    <span class="text-[10px] text-slate-500">Record CRM</span>
                </div>
                <div class="hidden lg:block text-slate-600 text-lg">➔</div>
                <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40 relative">
                    <span class="block text-xs font-bold text-teal-400 uppercase tracking-widest mb-1">02</span>
                    <strong class="text-sm text-slate-200 block">Job/WIP</strong>
                    <span class="text-[10px] text-slate-500">Log Scope</span>
                </div>
                <div class="hidden lg:block text-slate-600 text-lg">➔</div>
                <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40 relative">
                    <span class="block text-xs font-bold text-teal-400 uppercase tracking-widest mb-1">03</span>
                    <strong class="text-sm text-slate-200 block">Blockers</strong>
                    <span class="text-[10px] text-slate-500">Log Issues</span>
                </div>
                <div class="hidden lg:block text-slate-600 text-lg">➔</div>
                <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40 relative">
                    <span class="block text-xs font-bold text-teal-400 uppercase tracking-widest mb-1">04</span>
                    <strong class="text-sm text-slate-200 block">Invoice</strong>
                    <span class="text-[10px] text-slate-500">Xero Export</span>
                </div>
            </div>
        </section>

        <!-- Product Features Grid -->
        <section id="features" class="py-20 bg-slate-900/20 border-t border-slate-900 px-6 z-10 relative">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-black mb-4">Elite business control features</h2>
                    <p class="text-slate-400 text-sm max-w-2xl mx-auto">
                        Everything you need to maintain tight operations rhythm and financial health.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">📋</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">Client Register</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Keep every client, key contact person, relationship owner, and last follow-up contact date in one secure register.
                        </p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">💼</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">WIP / Job Tracker</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Turn scattered messages into auditable jobs with owners, percentages complete, target due dates, and priority status.
                        </p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">🛑</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">Blocker Escalation</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Flag bottlenecks in one click. Blocker descriptions, days open, and next action requirements immediately notify leadership.
                        </p>
                    </div>
                    <!-- Feature 4 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">🧾</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">Billing & Invoicing</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Automatically capture completed jobs. Generate branded invoices, attach PDFs, and deliver them to clients via queued emails.
                        </p>
                    </div>
                    <!-- Feature 5 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">📈</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">Financial Reports</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Monitor revenue with responsive custom SVG line trends, outstanding receivables trial balance aging ledgers, and staff velocity.
                        </p>
                    </div>
                    <!-- Feature 6 -->
                    <div class="p-6 rounded-xl border border-slate-800 bg-slate-900/40 text-left">
                        <span class="text-xl mb-4 block">🔗</span>
                        <h3 class="text-base font-bold text-slate-200 mb-2">Accounting Exports</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Seamlessly export invoice logs to standard CSV formats mapped exactly to Xero and MYOB bulk-import templates.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Case Study Block -->
        <section id="case-study" class="py-20 px-6 max-w-6xl mx-auto z-10 relative">
            <div class="border border-slate-800 bg-gradient-to-b from-slate-900 to-slate-950 rounded-2xl p-8 md:p-12 grid grid-cols-1 lg:grid-cols-5 gap-8 items-center text-left">
                <div class="lg:col-span-3">
                    <span class="text-xs font-bold text-teal-400 uppercase tracking-widest mb-2 block">Case Study / Pilot Partner</span>
                    <h2 class="text-3xl font-bold mb-4 text-slate-100">Inspired by SmartBiz Pacific</h2>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        BizPulse was shaped from a practical client monitoring and staff accountability workflow first used by <strong>SmartBiz Pacific</strong>, an accounting firm serving corporate clients and local SMEs in Papua New Guinea.
                    </p>
                    <p class="text-xs text-slate-500 italic leading-relaxed">
                        "The pilot moved client work away from scattered WhatsApp instructions and text messages into a single, unified operations register. The outcome resulted in zero missed deadlines, faster invoicing cycles, and improved fee realization."
                    </p>
                </div>
                <div class="lg:col-span-2 p-6 rounded-xl border border-slate-800 bg-slate-950/80">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Workspace Outcomes</span>
                    <ul class="space-y-3 text-xs text-slate-400">
                        <li class="flex items-center gap-2"><span class="text-teal-400">✓</span> 100% staff accountability</li>
                        <li class="flex items-center gap-2"><span class="text-teal-400">✓</span> No more buried client emails</li>
                        <li class="flex items-center gap-2"><span class="text-teal-400">✓</span> Standard Xero accounting synchronizations</li>
                        <li class="flex items-center gap-2"><span class="text-teal-400">✓</span> Clear weekly operations reviews</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-slate-900/40 border-y border-slate-900 px-6 z-10 relative">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-black mb-4">Simple pricing, built for Pacific SMEs</h2>
                <p class="text-slate-400 text-sm max-w-2xl mx-auto mb-12">
                    Pick a plan that fits your team roster. All plans include a 14-day free trial.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    <!-- Starter -->
                    <div class="p-8 rounded-xl border border-slate-800 bg-slate-950 flex flex-col justify-between text-left">
                        <div>
                            <span class="text-xs font-bold text-teal-400 uppercase block mb-1">Starter</span>
                            <strong class="text-3xl text-slate-100 font-bold block mb-2">K149 <span class="text-sm font-normal text-slate-400">/ month</span></strong>
                            <span class="text-xs text-slate-500 block mb-6">Setup from K1,500</span>
                            <ul class="space-y-3 text-xs text-slate-400 mb-8 border-t border-slate-900 pt-6">
                                <li>👥 1 to 5 active users</li>
                                <li>📁 Unlimited WIP tracker</li>
                                <li>💼 Client CRM register</li>
                                <li>🧾 Standard invoicing</li>
                            </ul>
                        </div>
                        <Link :href="route('register', { plan: 'starter' })" class="block w-full py-2.5 rounded-lg border border-slate-800 hover:border-slate-700 bg-slate-900 text-slate-300 font-semibold hover:text-slate-100 transition text-center text-sm">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- Business -->
                    <div class="p-8 rounded-xl border-2 border-teal-500 bg-slate-950 flex flex-col justify-between text-left shadow-[0_0_30px_rgba(20,184,166,0.08)] relative">
                        <span class="absolute top-0 right-6 translate-y-[-50%] px-3 py-0.5 rounded-full bg-teal-500 text-slate-950 font-bold text-[10px] uppercase tracking-wider">Most Popular</span>
                        <div>
                            <span class="text-xs font-bold text-teal-400 uppercase block mb-1">Business</span>
                            <strong class="text-3xl text-slate-100 font-bold block mb-2">K399 <span class="text-sm font-normal text-slate-400">/ month</span></strong>
                            <span class="text-xs text-slate-500 block mb-6">Setup from K2,500</span>
                            <ul class="space-y-3 text-xs text-slate-400 mb-8 border-t border-slate-900 pt-6">
                                <li>👥 6 to 15 active users</li>
                                <li>📁 Unlimited WIP tracker</li>
                                <li>🛑 Blocker escalation alerts</li>
                                <li>🧾 Dynamic PDF invoice delivery</li>
                                <li>🔗 Xero accounting export templates</li>
                            </ul>
                        </div>
                        <Link :href="route('register', { plan: 'business' })" class="block w-full py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-slate-950 font-bold transition text-center text-sm">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- Professional -->
                    <div class="p-8 rounded-xl border border-slate-800 bg-slate-950 flex flex-col justify-between text-left">
                        <div>
                            <span class="text-xs font-bold text-teal-400 uppercase block mb-1">Professional</span>
                            <strong class="text-3xl text-slate-100 font-bold block mb-2">K899 <span class="text-sm font-normal text-slate-400">/ month</span></strong>
                            <span class="text-xs text-slate-500 block mb-6">Setup from K5,000</span>
                            <ul class="space-y-3 text-xs text-slate-400 mb-8 border-t border-slate-900 pt-6">
                                <li>👥 16 to 40 active users</li>
                                <li>📁 Multiple client portals</li>
                                <li>📈 Real-time analytics & SVG charts</li>
                                <li>⚡ Staff velocity reporting</li>
                                <li>📞 Priority technical support</li>
                            </ul>
                        </div>
                        <Link :href="route('register', { plan: 'professional' })" class="block w-full py-2.5 rounded-lg border border-slate-800 hover:border-slate-700 bg-slate-900 text-slate-300 font-semibold hover:text-slate-100 transition text-center text-sm">
                            Start Free Trial
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enquiries Section / Contact Form -->
        <section id="contact" class="py-20 px-6 max-w-4xl mx-auto z-10 relative">
            <div class="bg-slate-900/40 border border-slate-800 backdrop-blur-md rounded-2xl p-8 md:p-12 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Request a customized demo</h2>
                <p class="text-slate-400 text-sm mb-8">
                    Have more than 40 users? Or would like a customized SOP workflow? Let's get in touch.
                </p>

                <div v-if="contactForm.submitted" class="p-6 rounded-lg bg-teal-950/20 border border-teal-900/50 text-teal-400 text-sm font-medium">
                    🎉 Thank you for your enquiry! The SmartBiz/BizPulse team will contact you in Port Moresby within 24 hours.
                </div>

                <form v-else @submit.prevent="submitContact" class="space-y-4 text-left">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Name</label>
                            <input type="text" v-model="contactForm.name" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Address</label>
                            <input type="email" v-model="contactForm.email" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Company Name</label>
                            <input type="text" v-model="contactForm.company" required class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Phone Number</label>
                            <input type="text" v-model="contactForm.phone" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Your Message / Custom Request</label>
                        <textarea v-model="contactForm.message" required rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 focus:outline-none focus:border-teal-500 text-sm text-slate-200"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-lg bg-teal-600 hover:bg-teal-700 text-slate-950 font-bold transition text-center text-sm shadow">
                        Submit Enquiry Request
                    </button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-900 py-12 px-6 md:px-12 text-center text-xs text-slate-500 z-10 relative">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-black text-slate-300 tracking-tight">BizPulse</span>
                    <span class="text-slate-600">|</span>
                    <span>No More Lost Instructions</span>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-slate-300">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-300">Terms of Service</a>
                </div>
                <div>
                    &copy; 2026 Penguin Pacific Ltd. Built for SMEs in PNG, Fiji, and across the Pacific.
                </div>
            </div>
        </footer>
    </div>
</template>
