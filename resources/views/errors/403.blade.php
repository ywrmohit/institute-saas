<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Restricted | {{ setting('app_name', 'Remax') }} Institute SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">
    <div class="max-w-md w-full text-center">
        <!-- Shield Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 mb-6 shadow-lg shadow-amber-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">
            403 &bull; Access Restricted
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">
            Workspace Permission Needed
        </h1>

        <p class="text-sm text-slate-400 mb-8 leading-relaxed">
            {{ $message ?? 'You are signed in, but your current account role does not have authorization to access this specific portal.' }}
        </p>

        <!-- Dynamic Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                @if (auth()->user()->isSuperAdmin())
                    <a href="{{ url('/admin') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-medium text-sm transition shadow-md">
                        Go to SaaS Central &rarr;
                    </a>
                @elseif (auth()->user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-medium text-sm transition shadow-md">
                        Go to Student Portal &rarr;
                    </a>
                @elseif (auth()->user()->franchise)
                    <a href="{{ url('/app/' . auth()->user()->franchise->slug) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                        Go to Institute Workspace &rarr;
                    </a>
                @else
                    <a href="{{ url('/app') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                        Go to My Workspace &rarr;
                    </a>
                @endif

                <a href="{{ url('/logout') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    Switch Account
                </a>
            @else
                <a href="{{ url('/app/login') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition shadow-md">
                    Sign In to Institute &rarr;
                </a>
                <a href="{{ url('/admin/login') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    Super Admin Sign In
                </a>
            @endauth
        </div>

        <div class="mt-8 pt-6 border-t border-slate-800/80">
            <a href="{{ url('/') }}" class="text-xs text-slate-500 hover:text-slate-400 transition">
                &larr; Return to Home Page
            </a>
        </div>
    </div>
</body>
</html>
