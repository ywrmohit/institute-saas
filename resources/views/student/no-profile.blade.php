<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile Pending | {{ setting('app_name', 'Remax') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-full flex flex-col justify-center items-center p-6 text-center text-slate-800 dark:text-slate-100">
    <div class="max-w-md bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
            ⚠️
        </div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Profile Setup Pending</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Your user account is verified, but no student admission profile is linked to your email address yet. Please contact your center administrator to complete your student profile setup.
        </p>
        <div class="mt-6 flex justify-center space-x-3">
            <form action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                    Logout
                </button>
            </form>
            <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition">
                Return Home
            </a>
        </div>
    </div>
</body>
</html>
