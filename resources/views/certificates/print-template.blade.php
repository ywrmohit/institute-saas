<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Plus+Jakarta+Sans:wght@400;600;700&family=Great+Vibes&display=swap" rel="stylesheet">
    <style>
        .font-serif-header { font-family: 'Cinzel', serif; }
        .font-script { font-family: 'Great Vibes', cursive; }
        .font-sans-body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: landscape; margin: 0; }
        }
    </style>
</head>
<body class="bg-slate-100 flex flex-col items-center justify-center min-h-screen p-4 sm:p-8 font-sans-body">

    <!-- Action Bar (Hidden on print) -->
    <div class="no-print max-w-5xl w-full mb-4 flex items-center justify-between bg-white px-6 py-3 rounded-xl shadow-sm border border-slate-200">
        <div class="text-xs text-slate-600">
            Certificate Number: <span class="font-mono font-bold text-blue-700">{{ $certificate->certificate_number }}</span>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('certificate.verify', ['code' => $certificate->verification_code]) }}" target="_blank" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                Test Public Verification &rarr;
            </a>
            <button onclick="window.print()" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Certificate</span>
            </button>
        </div>
    </div>

    <!-- Official Certificate Document (Landscape A4 proportion) -->
    <div class="relative bg-white w-full max-w-5xl aspect-[1.414/1] shadow-2xl rounded-2xl overflow-hidden p-8 sm:p-12 border-8 border-double border-blue-900 flex flex-col justify-between text-slate-800">
        <!-- Inner Decorative Border -->
        <div class="absolute inset-3 border-2 border-amber-500/70 pointer-events-none rounded-xl"></div>
        <div class="absolute inset-5 border border-blue-800/30 pointer-events-none rounded-lg"></div>

        <!-- Corner Ornaments -->
        <div class="absolute top-5 left-5 w-12 h-12 border-t-4 border-l-4 border-amber-600 rounded-tl-lg pointer-events-none"></div>
        <div class="absolute top-5 right-5 w-12 h-12 border-t-4 border-r-4 border-amber-600 rounded-tr-lg pointer-events-none"></div>
        <div class="absolute bottom-5 left-5 w-12 h-12 border-b-4 border-l-4 border-amber-600 rounded-bl-lg pointer-events-none"></div>
        <div class="absolute bottom-5 right-5 w-12 h-12 border-b-4 border-r-4 border-amber-600 rounded-br-lg pointer-events-none"></div>

        <!-- Background Watermark Emblem -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>

        <!-- Certificate Header -->
        <div class="text-center relative z-10 pt-2">
            <div class="flex items-center justify-center space-x-3 mb-2">
                <div class="w-12 h-12 rounded-full bg-blue-900 text-amber-400 flex items-center justify-center font-bold text-xl shadow-md border-2 border-amber-400">
                    ★
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-wider text-blue-950 font-serif-header">
                        {{ $certificate->franchise->name }}
                    </h2>
                    <p class="text-xs uppercase tracking-widest font-semibold text-amber-700">
                        {{ $certificate->branch->name }} &bull; Accredited Training Network
                    </p>
                </div>
            </div>

            <div class="my-4">
                <h1 class="text-3xl sm:text-4xl font-serif-header uppercase tracking-widest text-slate-900 font-bold">
                    Certificate of Completion
                </h1>
                <p class="text-xs uppercase tracking-widest text-slate-400 mt-1 font-semibold">
                    This is proudly conferred upon
                </p>
            </div>
        </div>

        <!-- Recipient & Course Details -->
        <div class="text-center relative z-10 my-auto py-2">
            <h3 class="text-4xl sm:text-5xl font-extrabold text-blue-900 font-serif-header tracking-tight underline decoration-amber-400 decoration-2 underline-offset-8">
                {{ $certificate->student->full_name }}
            </h3>
            <p class="text-xs text-slate-500 mt-3 font-medium">
                Student Identification ID: <span class="font-mono font-bold text-slate-700">{{ $certificate->student->student_id_code }}</span>
            </p>

            <p class="max-w-2xl mx-auto mt-6 text-sm sm:text-base text-slate-700 leading-relaxed">
                For successfully fulfilling all prescribed academic and practical requirements for the credential program of
            </p>

            <h4 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif-header mt-2 tracking-wide">
                {{ $certificate->course->name }}
            </h4>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Course Category: {{ ucwords(str_replace('_', ' ', $certificate->course->type)) }} &bull; Completed with <span class="text-blue-900 font-bold">{{ $certificate->grade ?? 'Distinction' }}</span>
                @if($certificate->percentage) ({{ $certificate->percentage }}%) @endif
            </p>
        </div>

        <!-- Certificate Footer & Signatures -->
        <div class="relative z-10 pt-4 flex items-end justify-between border-t border-slate-200">
            <!-- Left: Issue Details & QR Code -->
            <div class="flex items-center space-x-4">
                <!-- SVG QR Code representation -->
                <div class="p-1.5 bg-white border border-slate-300 rounded-lg shadow-sm text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('certificate.verify', ['code' => $certificate->verification_code])) }}" alt="QR Code" class="w-16 h-16 sm:w-20 sm:h-20">
                    <span class="text-[9px] text-slate-400 block mt-0.5">Scan to Verify</span>
                </div>
                <div class="text-left text-xs text-slate-600 space-y-0.5">
                    <p><span class="font-bold text-slate-800">Date Issued:</span> {{ $certificate->issue_date->format('d/m/Y') }}</p>
                    <p><span class="font-bold text-slate-800">Completion:</span> {{ $certificate->completion_date->format('d/m/Y') }}</p>
                    <p class="font-mono text-[10px] text-slate-400">UUID: {{ substr($certificate->verification_code, 0, 18) }}...</p>
                    <p class="font-mono text-[10px] text-blue-700 font-bold">No: {{ $certificate->certificate_number }}</p>
                </div>
            </div>

            <!-- Center: Gold Foil Official Seal -->
            <div class="hidden sm:flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-400 to-amber-500 text-blue-950 flex flex-col items-center justify-center shadow-lg border-2 border-white ring-2 ring-amber-400">
                    <svg class="w-6 h-6 text-blue-950 mb-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-[8px] uppercase tracking-widest font-extrabold text-blue-950">VERIFIED</span>
                </div>
            </div>

            <!-- Right: Signatures -->
            <div class="flex items-center space-x-8 text-center">
                <div>
                    <div class="h-10 border-b border-slate-400 flex items-end justify-center">
                        <span class="font-script text-2xl text-blue-900 leading-none">R. K. Sharma</span>
                    </div>
                    <span class="text-[11px] font-bold text-slate-800 block mt-1 uppercase tracking-wider">Academic Director</span>
                    <span class="text-[9px] text-slate-400 block">{{ $certificate->franchise->name }}</span>
                </div>

                <div>
                    <div class="h-10 border-b border-slate-400 flex items-end justify-center">
                        <span class="font-script text-2xl text-blue-900 leading-none">Anand Verma</span>
                    </div>
                    <span class="text-[11px] font-bold text-slate-800 block mt-1 uppercase tracking-wider">Center Head</span>
                    <span class="text-[9px] text-slate-400 block">{{ $certificate->branch->name }}</span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
