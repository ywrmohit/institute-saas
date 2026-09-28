<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error | EduPulse Institute SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">
    <div class="max-w-md w-full text-center">
        <!-- Error Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 mb-6 shadow-lg shadow-rose-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20 mb-3">
            500 &bull; Server Encountered An Issue
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">
            Something Went Wrong
        </h1>

        <p class="text-sm text-slate-400 mb-8 leading-relaxed">
            An unexpected error occurred while processing your request. The technical team has logged this occurrence.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="javascript:location.reload()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                Try Reloading &rarr;
            </a>

            <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>
