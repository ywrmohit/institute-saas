<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Login | EduPulse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5 mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
            </div>
            <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">EduPulse</span>
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Student Learning Portal</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Access your courses, attendance, fees, exams, and certificates.</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white dark:bg-slate-800 py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-slate-200 dark:border-slate-700">
            @if ($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-xs text-red-600 dark:text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('student.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Student ID / Roll / Email
                    </label>
                    <input type="text" id="login" name="login" value="{{ old('login', 'student@apextech.com') }}" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Portal Password
                    </label>
                    <input type="password" id="password" name="password" value="password" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                        <span class="ml-2">Remember Me</span>
                    </label>
                    <a href="{{ route('certificate.verify') }}" class="font-semibold text-blue-600 hover:underline">Verify Certificate</a>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition transform hover:-translate-y-0.5">
                    Sign In to Student Dashboard &rarr;
                </button>
            </form>

            <!-- Quick Demo Credential Helper -->
            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-700 text-center">
                <span class="text-[11px] text-slate-400 uppercase tracking-wider block font-semibold mb-2">Pre-Loaded Demo Student</span>
                <button type="button" onclick="document.getElementById('login').value='student@apextech.com'; document.getElementById('password').value='password';" class="text-xs px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700/60 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-mono transition">
                    student@apextech.com / password
                </button>
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-500">
            Staff or Administrator? <a href="{{ url('/admin') }}" class="font-bold text-blue-600 hover:underline">Go to Admin Login</a>
        </p>
    </div>

</body>
</html>
