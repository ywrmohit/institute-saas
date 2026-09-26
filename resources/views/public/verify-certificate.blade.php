<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Certificate Verification | EduPulse Registry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-between text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Header -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/90 backdrop-blur-md">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                    E
                </div>
                <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">EduPulse <span class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Verification</span></span>
            </a>
            <div class="flex items-center space-x-3 text-xs font-semibold">
                <a href="{{ route('home') }}" class="text-slate-600 dark:text-slate-400 hover:text-blue-600">Home</a>
                <a href="{{ route('student.login') }}" class="text-blue-600 hover:underline">Student Portal</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- Search Card -->
            <div class="text-center mb-8">
                <div class="inline-flex p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 mb-4 border border-blue-100 dark:border-blue-900">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Public Certificate Registry</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-md mx-auto">
                    Verify the authenticity of diplomas and certificates issued across our accredited franchise network.
                </p>

                <!-- Search Form -->
                <form action="{{ route('certificate.search') }}" method="POST" class="mt-6 flex flex-col sm:flex-row gap-2 max-w-lg mx-auto">
                    @csrf
                    <input type="text" name="code" value="{{ $code ?? '' }}" required placeholder="Enter Certificate # or Verification UUID" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition">
                        Verify Authenticity
                    </button>
                </form>
            </div>

            @if($searched)
                @if($certificate)
                    <!-- Verified Result Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-emerald-200 dark:border-emerald-800/60 overflow-hidden">
                        <!-- Top Authenticity Banner -->
                        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 text-white flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <span class="p-1.5 bg-white/20 rounded-full">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="font-bold text-base leading-tight">Authentic Certificate Verified</h3>
                                    <p class="text-xs text-emerald-100">Official record matched in central SaaS database.</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono bg-white/20 px-2.5 py-1 rounded-md uppercase font-bold tracking-wider">Active</span>
                        </div>

                        <!-- Details Grid -->
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-100 dark:border-slate-700/60 text-sm">
                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Candidate Name</span>
                                    <span class="text-base font-bold text-slate-900 dark:text-white">{{ $certificate->student->full_name }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Certificate Serial #</span>
                                    <span class="text-base font-mono font-bold text-blue-600 dark:text-blue-400">{{ $certificate->certificate_number }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Course Program</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $certificate->course->name }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Awarded Grade / Performance</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $certificate->grade ?? 'Pass' }} @if($certificate->percentage) ({{ $certificate->percentage }}%) @endif</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Issuing Franchise / Academy</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $certificate->franchise->name }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Branch Location</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $certificate->branch->name }} ({{ $certificate->branch->city ?? 'Main Campus' }})</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Course Completion Date</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $certificate->completion_date->format('F d, Y') }}</span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-400 block font-medium">Certificate Issue Date</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $certificate->issue_date->format('F d, Y') }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                                <div class="text-xs text-slate-400 max-w-sm text-center sm:text-left">
                                    <span class="font-semibold text-slate-500">Security Guarantee:</span> Private contact details and identification numbers remain strictly confidential.
                                </div>
                                <a href="{{ route('certificate.print', ['code' => $certificate->verification_code]) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-md transition flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>View Official Certificate &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Not Found Card -->
                    <div class="p-6 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-center">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-red-900 dark:text-red-200">No Matching Certificate Record Found</h3>
                        <p class="text-xs text-red-700 dark:text-red-300 mt-1 max-w-md mx-auto">
                            The credential identifier <span class="font-mono font-bold">"{{ $code }}"</span> does not correspond to any valid issued certificate in the system registry. Please double-check the code or contact the issuing franchise.
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} EduPulse Certified Registry. Official Public Verification.
    </footer>

</body>
</html>
