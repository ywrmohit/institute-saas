<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900" x-data="{ activeTab: 'personal' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Student Profile | {{ $student->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top App Navigation -->
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('student.dashboard') }}" class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-500/20 hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight leading-tight">{{ $student->franchise->name }}</h1>
                    <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 -mt-0.5">{{ $student->branch->name }} &bull; My Profile</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('student.dashboard') }}" class="px-3 py-1.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 bg-slate-100 dark:bg-slate-800 rounded-lg transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('student.id-card.print', ['id' => $student->id]) }}" target="_blank" class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span class="hidden sm:inline">Print ID Card</span>
                </a>
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">

        <!-- Flash Status Alerts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-sm font-semibold flex items-center space-x-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm font-semibold space-y-1">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc pl-6 text-xs font-normal">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Dossier Column (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Profile Avatar & ID Card Summary -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-24 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500"></div>

                    <!-- Photo Container with Upload Trigger -->
                    <div class="relative inline-block mt-8">
                        <div class="w-28 h-28 rounded-2xl border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto">
                            @if($student->photo_url)
                                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">
                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name ?? '', 0, 1) }}
                                </span>
                            @endif
                        </div>

                        <!-- Photo Upload Form Trigger -->
                        <form action="{{ route('student.profile.photo') }}" method="POST" enctype="multipart/form-data" class="absolute bottom-1 right-1" id="photoUploadForm">
                            @csrf
                            <label for="photoInput" class="w-8 h-8 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center cursor-pointer shadow-md transition transform hover:scale-105" title="Change Profile Photo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </label>
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden" onchange="document.getElementById('photoUploadForm').submit()">
                        </form>
                    </div>

                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mt-4">{{ $student->full_name }}</h2>
                    <p class="text-xs font-mono font-semibold text-blue-600 dark:text-blue-400 mt-0.5">{{ $student->student_id_code }} &bull; {{ $student->admission_number }}</p>

                    <div class="mt-4 flex justify-center gap-2">
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ ucfirst($student->status) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                            {{ $attendancePercentage }}% Attendance
                        </span>
                    </div>

                    <!-- Academic Affiliation -->
                    <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700 text-left space-y-3 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Campus Branch</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $student->branch->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Enrolled Date</span>
                            <span class="font-mono text-slate-700 dark:text-slate-200">{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Official Portal Email</span>
                            <span class="font-mono text-slate-700 dark:text-slate-200 select-all">{{ $student->email ?? $user->email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Phone</span>
                            <span class="font-mono text-slate-700 dark:text-slate-200">{{ $student->phone }}</span>
                        </div>
                    </div>

                    <!-- Quick Print ID Action -->
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('student.id-card.print', ['id' => $student->id]) }}" target="_blank" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Open CR80 PVC Student ID Card</span>
                        </a>
                    </div>
                </div>

                <!-- Enrolled Programs Card -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>📚</span>
                        <span>Enrolled Academics</span>
                    </h3>
                    @forelse($student->enrollments as $enrollment)
                        <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-1">
                            <div class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $enrollment->course->name }}</div>
                            <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold">{{ $enrollment->batch?->name ?? 'Standard Batch' }}</div>
                            @if($enrollment->batch?->trainer)
                                <div class="text-[10px] text-slate-400 flex items-center space-x-1 pt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Trainer: {{ $enrollment->batch->trainer->name }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No active course enrollments found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Right Profile Content (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Navigation Tabs -->
                <div class="flex items-center space-x-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Personal Details</span>
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Security & Password</span>
                    </button>
                    <button @click="activeTab = 'credentials'" :class="activeTab === 'credentials' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Certificates & Credentials</span>
                    </button>
                </div>

                <!-- TAB 1: Personal Details Form -->
                <div x-show="activeTab === 'personal'" class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Personal & Contact Details</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Keep your contact and guardian info updated for important academic notices and fee receipts.</p>
                    </div>

                    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">First Name *</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="last_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Mobile Phone *</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="gender" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Gender *</label>
                                <select id="gender" name="gender" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="qualification" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Highest Qualification</label>
                                <input type="text" id="qualification" name="qualification" value="{{ old('qualification', $student->qualification) }}" placeholder="e.g. 10th / 12th / Graduate" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="guardian_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Parent / Guardian Name</label>
                                <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="guardian_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Parent / Guardian Phone (Emergency)</label>
                                <input type="tel" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Current Residential Address</label>
                            <textarea id="address" name="address" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $student->address) }}</textarea>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Profile Changes</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Security & Password -->
                <div x-show="activeTab === 'security'" x-cloak class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Change Account Password</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ensure your account is protected with a secure password known only to you.</p>
                    </div>

                    <form action="{{ route('student.profile.password') }}" method="POST" class="space-y-5 max-w-lg">
                        @csrf

                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Current Password *</label>
                            <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">New Password (Min 6 characters) *</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Confirm New Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 3: Certificates & Credentials -->
                <div x-show="activeTab === 'credentials'" x-cloak class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Verifiable Diplomas & Credentials</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Official certificates issued by your franchise center with tamper-proof security tokens.</p>
                    </div>

                    <div class="space-y-4 pt-2">
                        @forelse($certificates as $cert)
                            <div class="p-5 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/50 dark:bg-emerald-950/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 uppercase">Verified Diploma</span>
                                        <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300">{{ $cert->certificate_number }}</span>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $cert->course->name }}</h4>
                                    <p class="text-xs text-slate-500">Issued on {{ \Carbon\Carbon::parse($cert->issue_date)->format('d M, Y') }} &bull; Grade: <strong>{{ $cert->grade }}</strong> ({{ $cert->percentage }}%)</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('certificate.print', ['code' => $cert->verification_code]) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center space-x-1.5 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Print Certificate</span>
                                    </a>
                                    <a href="{{ route('certificate.verify', ['code' => $cert->verification_code]) }}" target="_blank" class="px-4 py-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 hover:bg-blue-100 text-xs font-bold rounded-xl transition">
                                        Verify
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400">
                                No certificates issued yet. Pass your course modules and assessments to receive official diplomas.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $student->franchise->name }}. Powered by {{ setting('app_name', 'Remax') }} Institute SaaS.
    </footer>

</body>
</html>
