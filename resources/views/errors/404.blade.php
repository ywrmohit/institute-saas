<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | EduPulse Institute SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">
    <div class="max-w-md w-full text-center">
        <!-- Search Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-6 shadow-lg shadow-blue-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">
            404 &bull; Page Not Found
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">
            Lost in the Campus?
        </h1>

        <p class="text-sm text-slate-400 mb-8 leading-relaxed">
            The page or workspace URL you requested doesn't exist or may have been moved.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                @if (auth()->user()->isSuperAdmin())
                    <a href="{{ url('/admin') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                        SaaS Dashboard &rarr;
                    </a>
                @elseif (auth()->user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                        Student Portal &rarr;
                    </a>
                @elseif (auth()->user()->franchise)
                    <a href="{{ url('/app/' . auth()->user()->franchise->slug) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                        Institute Workspace &rarr;
                    </a>
                @endif
            @endauth

            <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                Return to Home
            </a>
        </div>
    </div>
</body>
</html>
