<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900" x-data="{ currentTab: 'academics' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->full_name }} | Student Learning Portal</title>
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
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight leading-tight">{{ $student->franchise->name }}</h1>
                    <p class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 -mt-0.5">{{ $student->branch->name }} &bull; Student Portal</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('certificate.verify') }}" class="hidden sm:inline-flex text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600">
                    Verify Certificate
                </a>
                <div class="flex items-center space-x-3 pl-3 border-l border-slate-200 dark:border-slate-700">
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-bold text-slate-900 dark:text-white block leading-tight">{{ $student->full_name }}</span>
                        <span class="text-[10px] font-mono text-slate-400">{{ $student->student_id_code }}</span>
                    </div>
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                    @else
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 font-bold flex items-center justify-center text-xs">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name ?? '', 0, 1) }}
                        </div>
                    @endif
                    <form action="{{ route('student.logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-6">

        <!-- Active Announcements Banner (if any) -->
        @if($announcements->count() > 0)
            <div class="space-y-2">
                @foreach($announcements as $announcement)
                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 flex items-start space-x-3">
                        <span class="text-xl">📢</span>
                        <div class="flex-1">
                            <h4 class="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider">{{ $announcement->title }}</h4>
                            <p class="text-xs text-blue-800 dark:text-blue-200 mt-0.5 leading-relaxed">{{ $announcement->content }}</p>
                        </div>
                        <span class="text-[10px] text-blue-600 font-medium">{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Student Profile Banner & Core Metrics -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Avatar" class="w-20 h-20 rounded-2xl object-cover border-2 border-white/50 shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl font-bold text-white shadow-md">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name ?? '', 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-2xl font-extrabold">{{ $student->full_name }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500 text-white shadow-sm">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-blue-100 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                            <span><strong>Roll ID:</strong> {{ $student->student_id_code }}</span>
                            <span><strong>Admission:</strong> {{ $student->admission_number }}</span>
                            <span><strong>Branch:</strong> {{ $student->branch->name }}</span>
                            <span><strong>Enrolled Since:</strong> {{ $student->admission_date->format('M Y') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Fast Summary Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Attendance</span>
                        <span class="text-xl font-black {{ $attendancePercentage >= 75 ? 'text-emerald-300' : 'text-amber-300' }}">{{ $attendancePercentage }}%</span>
                    </div>

                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Enrolled Courses</span>
                        <span class="text-xl font-black text-white">{{ $enrollments->count() }}</span>
                    </div>

                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Fee Paid</span>
                        <span class="text-xl font-black text-emerald-300">₹{{ number_format($totalPaid, 0) }}</span>
                    </div>

                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-200 block">Certificates</span>
                        <span class="text-xl font-black text-amber-300">{{ $certificates->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 dark:border-slate-800 flex space-x-2 sm:space-x-4 overflow-x-auto text-xs font-bold">
            <button @click="currentTab = 'academics'" :class="currentTab === 'academics' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>📚</span> <span>Courses & Batches</span>
            </button>
            <button @click="currentTab = 'attendance'" :class="currentTab === 'attendance' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>📅</span> <span>Attendance Record</span>
            </button>
            <button @click="currentTab = 'fees'" :class="currentTab === 'fees' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>💳</span> <span>Fees & Receipts</span>
            </button>
            <button @click="currentTab = 'exams'" :class="currentTab === 'exams' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>📝</span> <span>Exams & Results</span>
            </button>
            <button @click="currentTab = 'materials'" :class="currentTab === 'materials' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>📁</span> <span>Study Materials</span>
            </button>
            <button @click="currentTab = 'certificates'" :class="currentTab === 'certificates' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="py-3 px-3 border-b-2 whitespace-nowrap transition flex items-center space-x-1.5">
                <span>🏆</span> <span>Certificates</span>
            </button>
        </div>

        <!-- TAB 1: ACADEMICS / ENROLLED COURSES -->
        <div x-show="currentTab === 'academics'" x-cloak class="space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Active Enrollments & Class Schedule</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($enrollments as $enrollment)
                    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                    {{ ucwords(str_replace('_', ' ', $enrollment->course->type)) }}
                                </span>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $enrollment->course->name }}</h4>
                                <p class="text-xs text-slate-400 font-mono">Code: {{ $enrollment->course->code }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $enrollment->status === 'enrolled' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>

                        <!-- Batch Info Box -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Classroom Batch:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $enrollment->batch->name }} ({{ $enrollment->batch->batch_code }})</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Class Timings:</span>
                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $enrollment->batch->time_slot }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Faculty / Trainer:</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $enrollment->batch->trainer?->name ?? 'Assigned Faculty' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Start Date:</span>
                                <span class="text-slate-600 dark:text-slate-400">{{ $enrollment->batch->start_date->format('d M, Y') }}</span>
                            </div>
                        </div>

                        @if($enrollment->course->modules->count() > 0)
                            <div>
                                <h5 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Curriculum Syllabus:</h5>
                                <div class="space-y-1.5">
                                    @foreach($enrollment->course->modules as $module)
                                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900/40 rounded-lg text-xs flex justify-between items-center">
                                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $module->title }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $module->duration_hours }} Hours</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 col-span-2">
                        No course enrollments found.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: ATTENDANCE -->
        <div x-show="currentTab === 'attendance'" x-cloak class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daily Attendance Record</h3>
                <div class="text-xs">
                    Overall Attendance: <strong class="{{ $attendancePercentage >= 75 ? 'text-emerald-600' : 'text-amber-600' }} text-sm">{{ $attendancePercentage }}%</strong>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-400 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5">Date</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Batch</th>
                            <th class="p-3.5">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentAttendances as $att)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-750 transition">
                                <td class="p-3.5 font-bold text-slate-800 dark:text-slate-200">{{ $att->date->format('d M, Y (D)') }}</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($att->status === 'late' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : ($att->status === 'leave' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300')) }}">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-500">{{ $att->batch->name ?? '-' }}</td>
                                <td class="p-3.5 text-slate-400">{{ $att->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">No attendance marked yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: FEES & RECEIPTS -->
        <div x-show="currentTab === 'fees'" x-cloak class="space-y-6">
            <!-- Balance Card -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm text-center">
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Total Invoiced Fees</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">₹{{ number_format($totalFees, 2) }}</span>
                </div>
                <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm text-center">
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Total Settled Fees</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">₹{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm text-center">
                    <span class="text-xs text-slate-400 uppercase font-semibold block">Remaining Overdue</span>
                    <span class="text-2xl font-black {{ $totalPending > 0 ? 'text-red-600' : 'text-emerald-600' }} mt-1 block">₹{{ number_format($totalPending, 2) }}</span>
                </div>
            </div>

            <!-- Invoices & Installments -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Fee Invoices & Scheduled Installments</h4>
                @foreach($invoices as $invoice)
                    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-base font-bold text-slate-900 dark:text-white">{{ $invoice->title }}</h5>
                                <p class="text-xs font-mono text-slate-400">Inv #: {{ $invoice->invoice_number }} &bull; Due: {{ $invoice->due_date?->format('d M, Y') ?? 'Immediate' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'partially_paid' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucwords(str_replace('_', ' ', $invoice->status)) }}
                            </span>
                        </div>

                        <!-- Installments Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($invoice->installments as $inst)
                                <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                                    <div class="flex justify-between font-bold">
                                        <span class="text-slate-700 dark:text-slate-300">Installment #{{ $inst->installment_number }}</span>
                                        <span class="{{ $inst->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($inst->status) }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-500">
                                        <span>Amount:</span>
                                        <span class="font-mono font-bold text-slate-900 dark:text-white">₹{{ number_format($inst->amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-400 text-[11px]">
                                        <span>Due Date:</span>
                                        <span>{{ $inst->due_date->format('d M, Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Payment Receipts History -->
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Issued Payment Receipts</h4>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-900 text-slate-400 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="p-3.5">Receipt #</th>
                                <th class="p-3.5">Date</th>
                                <th class="p-3.5">Amount</th>
                                <th class="p-3.5">Mode</th>
                                <th class="p-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($payments as $pay)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-750 transition">
                                    <td class="p-3.5 font-mono font-bold text-blue-600">{{ $pay->receipt_number }}</td>
                                    <td class="p-3.5 text-slate-600 dark:text-slate-300">{{ $pay->payment_date->format('d M, Y') }}</td>
                                    <td class="p-3.5 font-bold font-mono text-emerald-600">₹{{ number_format($pay->amount, 2) }}</td>
                                    <td class="p-3.5 uppercase font-semibold text-slate-500">{{ $pay->payment_method }}</td>
                                    <td class="p-3.5 text-right">
                                        <a href="{{ route('receipt.print', ['receiptNumber' => $pay->receipt_number]) }}" target="_blank" class="px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-blue-600 hover:text-white rounded-lg transition font-bold text-[11px] inline-flex items-center space-x-1">
                                            <span>Print Receipt</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">No payment receipts issued yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: EXAMS & RESULTS -->
        <div x-show="currentTab === 'exams'" x-cloak class="space-y-6">
            <!-- Available Tests to Take -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Scheduled Assessments & Online MCQ Tests</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($exams as $exam)
                        @php
                            $result = $examResults->firstWhere('exam_id', $exam->id);
                        @endphp
                        <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
                                        {{ strtoupper($exam->exam_type) }} Test
                                    </span>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-base mt-1">{{ $exam->title }}</h4>
                                    <p class="text-xs text-slate-400 font-medium">Marks: {{ $exam->total_marks }} (Pass: {{ $exam->passing_marks }}) &bull; Duration: {{ $exam->duration_minutes }} Min</p>
                                </div>
                                @if($result)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $result->status === 'pass' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($result->status) }}
                                    </span>
                                @endif
                            </div>

                            @if($result)
                                <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl text-xs flex justify-between items-center">
                                    <span class="text-slate-500">Your Score: <strong class="text-slate-900 dark:text-white">{{ $result->marks_obtained }} / {{ $result->total_marks }} ({{ $result->percentage }}%)</strong></span>
                                    <span class="font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Grade {{ $result->grade }}</span>
                                </div>
                            @else
                                <div class="pt-2">
                                    <a href="{{ route('student.exam.take', ['id' => $exam->id]) }}" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 text-center block shadow-md shadow-blue-500/20 transition">
                                        Start Online Assessment &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 col-span-2">
                            No examinations scheduled at this time.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 5: STUDY MATERIALS -->
        <div x-show="currentTab === 'materials'" x-cloak class="space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Assigned Courseware & Video Lectures</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($studyMaterials as $mat)
                    <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $mat->type === 'pdf' ? 'bg-red-100 text-red-700' : ($mat->type === 'video_link' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ strtoupper($mat->type) }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $mat->created_at->format('d M, Y') }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $mat->title }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ $mat->description ?? 'Course syllabus lecture note' }}</p>
                        </div>

                        <div>
                            @if($mat->type === 'video_link' && $mat->external_url)
                                <a href="{{ $mat->external_url }}" target="_blank" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold text-center block transition flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                    <span>Watch Video Lecture</span>
                                </a>
                            @elseif($mat->file_path)
                                <a href="{{ asset('storage/' . $mat->file_path) }}" download class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold text-center block transition flex items-center justify-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Download Resource</span>
                                </a>
                            @else
                                <span class="text-xs text-slate-400 block text-center">Classroom Notes</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 col-span-3">
                        No study materials uploaded for your current batches yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 6: CERTIFICATES -->
        <div x-show="currentTab === 'certificates'" x-cloak class="space-y-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Awarded Diplomas & Verified Certificates</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($certificates as $cert)
                    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-amber-300 dark:border-amber-800/80 space-y-4 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-amber-400/10 pointer-events-none"></div>

                        <div class="flex items-start justify-between">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                    Official Credential
                                </span>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $cert->course->name }}</h4>
                                <p class="text-xs font-mono font-bold text-blue-600">{{ $cert->certificate_number }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                Issued
                            </span>
                        </div>

                        <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Awarded Performance:</span>
                                <span class="font-bold text-emerald-600">{{ $cert->grade ?? 'Distinction' }} @if($cert->percentage) ({{ $cert->percentage }}%) @endif</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Date of Completion:</span>
                                <span class="text-slate-700 dark:text-slate-300">{{ $cert->completion_date->format('d M, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Date of Issue:</span>
                                <span class="text-slate-700 dark:text-slate-300">{{ $cert->issue_date->format('d M, Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pt-2">
                            <a href="{{ route('certificate.print', ['code' => $cert->verification_code]) }}" target="_blank" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold text-center transition flex items-center justify-center space-x-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>View & Print</span>
                            </a>
                            <a href="{{ route('certificate.verify', ['code' => $cert->verification_code]) }}" target="_blank" class="px-4 py-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 hover:bg-blue-100 rounded-xl text-xs font-bold transition">
                                Public Verification
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 col-span-2">
                        No certificates issued yet. Complete all course modules and assessments to receive your diploma.
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ $student->franchise->name }}. Powered by {{ setting('app_name', 'Remax') }} Institute SaaS.
    </footer>

</body>
</html>
