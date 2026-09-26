<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPulse | Multi-Tenant Institute & Franchise Management SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col justify-between text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">EduPulse</span>
                    <span class="text-xs uppercase tracking-widest font-semibold text-slate-400 block -mt-1">SaaS & Franchise</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('certificate.verify') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-blue-600 transition">
                    <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Verify Certificate
                </a>
                <a href="{{ route('student.login') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 rounded-lg transition border border-blue-200 dark:border-blue-900">
                    Student Portal
                </a>
                <a href="{{ url('/admin') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md shadow-blue-500/20 transition">
                    Admin / Staff Login &rarr;
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative pt-12 pb-20 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-100 dark:bg-blue-950/70 border border-blue-300 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-xs font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                    <span>Complete Multi-Tenant Architecture & Franchise Management</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 dark:text-white max-w-4xl mx-auto leading-tight sm:leading-none">
                    Next-Gen Management for <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 bg-clip-text text-transparent">Institutes & Franchises</span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto leading-relaxed">
                    Operate central SaaS operations with strict data isolation for every franchise. Empower academies to run branch admissions, trainers, course batches, attendance, fee installments, online MCQ assessments, and QR-verifiable diplomas.
                </p>

                <!-- Action CTA buttons -->
                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <a href="{{ url('/admin') }}" class="px-6 py-3.5 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xl shadow-blue-500/30 transition transform hover:-translate-y-0.5 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Super Admin Panel</span>
                    </a>

                    <a href="{{ url('/app') }}" class="px-6 py-3.5 text-base font-bold text-slate-800 dark:text-white bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 border border-slate-300 dark:border-slate-700 rounded-xl shadow-sm transition flex items-center space-x-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Franchise & Branch Hub</span>
                    </a>

                    <a href="{{ route('student.login') }}" class="px-6 py-3.5 text-base font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 rounded-xl border border-blue-200 dark:border-blue-900 transition flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Student Portal</span>
                    </a>

                    <a href="{{ route('certificate.verify') }}" class="px-6 py-3.5 text-base font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-xl transition flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Verify Certificate</span>
                    </a>
                </div>

                <!-- Live Demo Access Card -->
                <div class="mt-14 max-w-4xl mx-auto bg-white dark:bg-slate-800/90 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700/80 p-6 sm:p-8 text-left">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                                <span>Pre-Seeded Demo Access Credentials</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Use these credentials to experience each role in the multi-tenant architecture.</p>
                        </div>
                        <span class="text-xs font-mono font-bold bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-md text-slate-600 dark:text-slate-300">Password: password</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        <!-- Super Admin -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300 uppercase">Super Admin</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">Platform Central</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">admin@edupulse.io</p>
                            <a href="{{ url('/admin') }}" class="mt-3 block text-xs font-bold text-blue-600 hover:underline">Launch /admin &rarr;</a>
                        </div>

                        <!-- Franchise Owner -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 uppercase">Franchise Owner</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">Apex Tech Institute</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">owner@apextech.com</p>
                            <a href="{{ url('/app') }}" class="mt-3 block text-xs font-bold text-blue-600 hover:underline">Launch /app &rarr;</a>
                        </div>

                        <!-- Branch Admin -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 uppercase">Branch Admin</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">Downtown Campus</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">admin@apex-downtown.com</p>
                            <a href="{{ url('/app') }}" class="mt-3 block text-xs font-bold text-blue-600 hover:underline">Launch /app &rarr;</a>
                        </div>

                        <!-- Trainer -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-cyan-100 text-cyan-700 dark:bg-cyan-950 dark:text-cyan-300 uppercase">Trainer / Faculty</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">Web Dev Faculty</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">trainer@apextech.com</p>
                            <a href="{{ url('/app') }}" class="mt-3 block text-xs font-bold text-blue-600 hover:underline">Launch /app &rarr;</a>
                        </div>

                        <!-- Accountant -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 uppercase">Accountant</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">Fee Operations</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">accountant@apextech.com</p>
                            <a href="{{ url('/app') }}" class="mt-3 block text-xs font-bold text-blue-600 hover:underline">Launch /app &rarr;</a>
                        </div>

                        <!-- Student -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 uppercase">Student</span>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200 mt-2">DCA Student</h4>
                            <p class="text-xs font-mono text-slate-500 mt-1 select-all">student@apextech.com</p>
                            <a href="{{ route('student.login') }}" class="mt-3 block text-xs font-bold text-purple-600 hover:underline">Launch /student &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Pillars Grid -->
        <section class="py-16 bg-white dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Built for Operational Scale & Strict Security</h2>
                    <p class="mt-4 text-base text-slate-600 dark:text-slate-400">Everything needed to launch, manage, and expand a nationwide computer institute or coaching network.</p>
                </div>

                <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl mb-4">
                            🏢
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Strict Multi-Tenancy</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Database queries and Filament panels are strictly isolated via Eloquent global scopes and backend policies. A franchise never accesses another franchise's data.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xl mb-4">
                            💳
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Fees & Installment Engine</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Automated course fees, registration charges, custom installment schedules, late fee alerts, printable thermal/A4 receipts, and Razorpay readiness.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xl mb-4">
                            🏆
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">QR Certificates & Verification</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Issue tamper-resistant certificates with unique security tokens and QR codes. Includes a public verification page protecting private student PII.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl mb-4">
                            📝
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Online MCQ Exam Player</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Build question banks, schedule batch tests, auto-evaluate submissions, assign grades, and show instant feedback directly in the student portal.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xl mb-4">
                            📱
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Modern Student Portal</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            No rigid admin panel for learners. A responsive, mobile-first card interface for timetables, attendance %, notes, video links, receipts, and exams.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xl mb-4">
                            🤖
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">AI-Ready Retention Insights</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            Detect student dropout risk from attendance lags, exam scores, and payment delays with clean service hooks ready for LLM expansion.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} EduPulse SaaS Platform. All rights reserved.</p>
            <div class="flex items-center space-x-6 mt-4 sm:mt-0 font-medium">
                <a href="{{ route('certificate.verify') }}" class="hover:text-blue-600">Public Verification</a>
                <a href="{{ route('student.login') }}" class="hover:text-blue-600">Student Portal</a>
                <a href="{{ url('/admin') }}" class="hover:text-blue-600">Platform Admin</a>
            </div>
        </div>
    </footer>

</body>
</html>
