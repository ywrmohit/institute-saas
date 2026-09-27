<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card - {{ $student->full_name }} ({{ $student->student_id_code }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .id-card-wrapper {
                box-shadow: none !important;
            }
        }
        .id-card {
            width: 85.6mm;
            height: 54mm;
            border-radius: 4mm;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 font-sans text-slate-800">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <div class="flex items-center space-x-3">
            <span class="p-2 rounded-lg bg-blue-600 text-white shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
            </span>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Official Student Identity Card</h1>
                <p class="text-xs text-slate-500">CR80 PVC Standard Dimensions (85.6mm × 54mm) • Ready for Color Printing</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm flex items-center space-x-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print ID Card</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-semibold rounded-lg transition">
                Close
            </button>
        </div>
    </div>

    <!-- ID Card Container -->
    <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-center gap-8">

        <!-- FRONT SIDE -->
        <div class="id-card bg-white shadow-xl border border-slate-300 flex flex-col justify-between id-card-wrapper">
            <!-- Header Band -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 px-3 py-1.5 flex items-center justify-between text-white">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded-full bg-white text-blue-700 font-extrabold flex items-center justify-center text-xs shadow-inner">
                        EP
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-wider leading-none">EduPulse Institute SaaS</div>
                        <div class="text-[7.5px] text-blue-200 font-medium leading-tight">{{ $student->franchise?->name ?? 'Apex Tech Institute' }}</div>
                    </div>
                </div>
                <span class="text-[6.5px] font-bold uppercase bg-blue-500/40 px-1.5 py-0.5 rounded text-blue-100 tracking-wider">STUDENT ID</span>
            </div>

            <!-- Body Content -->
            <div class="px-3 py-1.5 flex gap-2.5 items-center flex-1">
                <!-- Photo & Roll -->
                <div class="flex flex-col items-center">
                    <div class="w-[20mm] h-[24mm] rounded border border-slate-300 bg-slate-100 overflow-hidden shadow-inner flex items-center justify-center">
                        @if($student->photo)
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-[26px] font-bold text-slate-400 uppercase">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <span class="text-[7px] font-mono font-bold text-slate-600 mt-1 bg-slate-100 px-1 rounded border border-slate-200">
                        {{ $student->student_id_code }}
                    </span>
                </div>

                <!-- Bio & Enrollment Details -->
                <div class="flex-1 text-[8px] leading-tight space-y-0.5">
                    <div>
                        <div class="text-[11px] font-extrabold text-slate-900 leading-tight">{{ $student->full_name }}</div>
                        <div class="text-[7.5px] text-blue-600 font-semibold">Adm No: <span class="font-mono text-slate-700">{{ $student->admission_number }}</span></div>
                    </div>

                    @php
                        $activeEnrollment = $student->enrollments()->latest()->first();
                        $courseName = $activeEnrollment?->course?->name ?? 'Certificate Course';
                        $batchName = $activeEnrollment?->batch?->name ?? 'Standard Batch';
                        $batchTime = $activeEnrollment?->batch?->timing ?? 'Regular Hours';
                    @endphp

                    <div class="pt-0.5 border-t border-slate-100 space-y-0.5">
                        <div class="flex justify-between"><span class="text-slate-400 font-medium">Course:</span> <span class="font-bold text-slate-800 text-right truncate max-w-[120px]">{{ $courseName }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400 font-medium">Batch:</span> <span class="font-semibold text-slate-700">{{ $batchName }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400 font-medium">Timing:</span> <span class="font-semibold text-slate-700">{{ $batchTime }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400 font-medium">Contact:</span> <span class="font-mono text-slate-700">{{ $student->phone }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400 font-medium">Branch:</span> <span class="font-medium text-slate-700">{{ $student->branch?->name ?? 'Main Campus' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Footer Strip with Barcode/QR Code Representation -->
            <div class="bg-slate-50 border-t border-slate-200 px-3 py-1 flex items-center justify-between text-[7px]">
                <div class="flex items-center space-x-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-slate-700 uppercase">Status: {{ ucfirst($student->status) }}</span>
                </div>
                <div class="font-mono text-slate-400 text-[6.5px]">
                    Valid: {{ date('Y') }} - {{ date('Y') + 1 }}
                </div>
            </div>
        </div>

        <!-- BACK SIDE -->
        <div class="id-card bg-white shadow-xl border border-slate-300 flex flex-col justify-between id-card-wrapper">
            <!-- Back Header -->
            <div class="bg-slate-800 px-3 py-1.5 text-white flex items-center justify-between">
                <span class="text-[8px] font-bold uppercase tracking-wider">Terms & Instructions</span>
                <span class="text-[7px] text-slate-400 font-mono">CR80-BACK</span>
            </div>

            <!-- Back Content -->
            <div class="px-3 py-2 flex-1 flex flex-col justify-between text-[7px] text-slate-600 leading-tight">
                <ul class="list-disc pl-3 space-y-0.5">
                    <li>This card is non-transferable and must be presented upon entering campus and test labs.</li>
                    <li>Loss of card must be reported to the branch office immediately.</li>
                    <li>Minimum 75% attendance is required to appear in final certification examinations.</li>
                </ul>

                <div class="bg-slate-50 p-1.5 rounded border border-slate-200 flex items-center justify-between mt-1">
                    <div>
                        <div class="text-[6.5px] uppercase font-bold text-slate-400">Emergency Contact:</div>
                        <div class="font-bold text-slate-800 text-[7.5px]">{{ $student->guardian_name ?? 'Branch Office' }}: {{ $student->guardian_phone ?? $student->phone }}</div>
                        <div class="text-[6.5px] text-slate-500 truncate max-w-[130px]">{{ $student->branch?->city ?? 'Campus Admin' }}, {{ $student->branch?->phone ?? '' }}</div>
                    </div>
                    <!-- QR Code Placeholder -->
                    <div class="w-7 h-7 bg-white border border-slate-300 p-0.5 flex flex-col items-center justify-center">
                        <svg class="w-full h-full text-slate-800" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h2v2h-2v-2zm-4 0h2v4h-2v-4zm4 4h2v2h-2v-2zm-2 2h2v2h-2v-2zm-2-2h2v2h-2v-2zm4 2h2v2h-2v-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Back Footer: Signatures -->
            <div class="border-t border-slate-200 px-3 py-1 flex items-center justify-between text-[6.5px] text-slate-400 bg-slate-50">
                <div>Student Signature</div>
                <div class="font-semibold text-slate-700">Authorized Signatory / Principal</div>
            </div>
        </div>

    </div>

    <!-- Quick Tips -->
    <div class="max-w-2xl mx-auto mt-8 text-center text-xs text-slate-500 no-print">
        💡 <strong>Print Tip:</strong> In your browser's Print dialog, select <em>Destination: Save as PDF</em> or your card printer. Ensure <em>Margins: None</em> or <em>Default</em> and <em>Background graphics: Checked</em> for optimal color saturation.
    </div>

</body>
</html>
