<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 dark:bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} | Online Examination</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } .font-mono { font-family: 'JetBrains Mono', monospace; }</style>
</head>
<body class="min-h-full flex flex-col justify-between text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Header Bar -->
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('student.dashboard') }}" class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    &larr; Exit
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
                    <p class="text-[11px] text-slate-400">Total Marks: {{ $exam->total_marks }} &bull; Passing: {{ $exam->passing_marks }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="px-3 py-1 bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 rounded-lg text-blue-700 dark:text-blue-300 text-xs font-mono font-bold flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $exam->duration_minutes }} Mins Allocated</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Question Form / Scorecard -->
    <main class="flex-1 max-w-4xl mx-auto px-4 py-8 w-full">

        @if(session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold flex items-center space-x-3">
                <span class="text-2xl">🎉</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($existingResult)
            <!-- Candidate Already Evaluated Scorecard -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8 text-center space-y-6">
                <div class="w-16 h-16 rounded-full {{ $existingResult->status === 'pass' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center mx-auto text-3xl">
                    {{ $existingResult->status === 'pass' ? '✓' : '✗' }}
                </div>

                <div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">Assessment Completed</h2>
                    <p class="text-xs text-slate-500 mt-1">Submitted on {{ $existingResult->updated_at->format('d M, Y h:i A') }}</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-xl mx-auto">
                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Marks Scored</span>
                        <span class="text-xl font-bold font-mono text-slate-900 dark:text-white">{{ $existingResult->marks_obtained }} / {{ $existingResult->total_marks }}</span>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Percentage</span>
                        <span class="text-xl font-bold font-mono text-blue-600">{{ $existingResult->percentage }}%</span>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Grade</span>
                        <span class="text-xl font-bold font-mono text-emerald-600">Grade {{ $existingResult->grade }}</span>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Result</span>
                        <span class="text-xl font-bold uppercase font-mono {{ $existingResult->status === 'pass' ? 'text-emerald-600' : 'text-red-600' }}">{{ $existingResult->status }}</span>
                    </div>
                </div>

                @if($existingResult->feedback)
                    <div class="p-4 bg-blue-50 dark:bg-blue-950/30 rounded-xl text-xs text-blue-900 dark:text-blue-300 max-w-md mx-auto">
                        <strong>Trainer Feedback:</strong> {{ $existingResult->feedback }}
                    </div>
                @endif

                <div class="pt-4">
                    <a href="{{ route('student.dashboard') }}" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                        Back to Student Dashboard &rarr;
                    </a>
                </div>
            </div>
        @else
            <!-- Online MCQ Exam Form -->
            <form action="{{ route('student.exam.submit', ['id' => $exam->id]) }}" method="POST" class="space-y-6" onsubmit="return confirm('Are you sure you want to finish and submit your exam?');">
                @csrf

                @if($exam->instructions)
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-xl text-xs text-amber-900 dark:text-amber-200 leading-relaxed">
                        <strong class="font-bold block mb-1">Exam Instructions:</strong>
                        {{ $exam->instructions }}
                    </div>
                @endif

                <div class="space-y-6">
                    @forelse($exam->questions as $index => $q)
                        <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                            <div class="flex items-start justify-between">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                                    <span class="text-blue-600 font-mono mr-1.5">Q{{ $index + 1 }}.</span> {{ $q->question_text }}
                                </h3>
                                <span class="text-[11px] font-mono px-2 py-0.5 bg-slate-100 dark:bg-slate-700 rounded text-slate-500 font-bold whitespace-nowrap ml-2">
                                    {{ $q->marks }} Mark{{ $q->marks > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div class="space-y-2.5 pt-1">
                                @if(is_array($q->options))
                                    @foreach($q->options as $optKey => $optVal)
                                        <label class="flex items-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-blue-50/50 dark:hover:bg-slate-700/50 cursor-pointer transition">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optKey }}" class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                            <span class="ml-3 text-xs font-bold text-slate-500 uppercase font-mono mr-2">({{ $optKey }})</span>
                                            <span class="text-xs text-slate-800 dark:text-slate-200 font-medium">{{ $optVal }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 text-slate-400">
                            No questions uploaded for this exam yet.
                        </div>
                    @endforelse
                </div>

                @if($exam->questions->count() > 0)
                    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-xs text-slate-500">Answered questions will be immediately evaluated upon submission.</span>
                        <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/20 transition">
                            Submit Examination &rarr;
                        </button>
                    </div>
                @endif
            </form>
        @endif

    </main>

</body>
</html>
