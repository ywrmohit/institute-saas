<div class="space-y-4 text-sm">
    <!-- Header Summary Card -->
    <div class="p-4 rounded-xl border {{ $insight['risk_level'] === 'high' ? 'bg-red-50 border-red-200 dark:bg-red-950/30 dark:border-red-900' : ($insight['risk_level'] === 'medium' ? 'bg-amber-50 border-amber-200 dark:bg-amber-950/30 dark:border-amber-900' : 'bg-emerald-50 border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-900') }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg {{ $insight['risk_level'] === 'high' ? 'bg-red-500 text-white' : ($insight['risk_level'] === 'medium' ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-base text-gray-900 dark:text-gray-100">{{ $student->full_name }}</h4>
                    <p class="text-xs text-gray-500">Roll: {{ $student->student_id_code }} | Branch: {{ $student->branch?->name }}</p>
                </div>
            </div>
            <div>
                <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider {{ $insight['risk_level'] === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : ($insight['risk_level'] === 'medium' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200') }}">
                    {{ ucfirst($insight['risk_level']) }} Risk
                </span>
            </div>
        </div>
        <p class="mt-3 text-xs leading-relaxed text-gray-700 dark:text-gray-300">
            {{ $insight['recommendation'] }}
        </p>
    </div>

    <!-- Core Metrics Grid -->
    <div class="grid grid-cols-3 gap-3">
        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
            <span class="text-xs text-gray-500 block">Attendance Rate</span>
            <span class="text-lg font-bold {{ $insight['attendance_percentage'] < 60 ? 'text-red-600' : ($insight['attendance_percentage'] < 75 ? 'text-amber-600' : 'text-emerald-600') }}">
                {{ $insight['attendance_percentage'] }}%
            </span>
        </div>

        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
            <span class="text-xs text-gray-500 block">Exam Avg Score</span>
            <span class="text-lg font-bold {{ $insight['average_exam_percentage'] < 50 ? 'text-red-600' : 'text-blue-600' }}">
                {{ $insight['average_exam_percentage'] }}%
            </span>
        </div>

        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
            <span class="text-xs text-gray-500 block">Pending Fees</span>
            <span class="text-lg font-bold {{ $insight['pending_fee'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                ₹{{ number_format($insight['pending_fee'], 2) }}
            </span>
        </div>
    </div>

    <!-- Detected Risk Factors -->
    <div>
        <h5 class="font-semibold text-xs text-gray-500 uppercase tracking-wider mb-2">Detected Predictive Signals:</h5>
        @if(count($insight['risk_factors']) > 0)
            <ul class="space-y-1.5">
                @foreach($insight['risk_factors'] as $factor)
                    <li class="flex items-center space-x-2 text-xs text-gray-700 dark:text-gray-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        <span>{{ $factor }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-xs text-emerald-600 dark:text-emerald-400">✓ No risk flags detected. Student is on track.</p>
        @endif
    </div>
</div>
