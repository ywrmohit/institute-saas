<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Top Institute Hero Dossier & Infrastructure Metrics -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-8 text-white shadow-xl border border-blue-900/40">
            <!-- Decorative Background glow -->
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Institute Brand & Key Badges -->
                <div class="flex items-start sm:items-center space-x-5">
                    @if($franchise->logo)
                        <img src="{{ asset('storage/' . $franchise->logo) }}" alt="Institute Logo" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover bg-white/10 p-1 border-2 border-white/20 shadow-lg">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-3xl font-black text-white shadow-lg border-2 border-white/20">
                            {{ substr($franchise->name, 0, 2) }}
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $franchise->name }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                {{ ucfirst($franchise->status) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                Code: {{ $franchise->code }}
                            </span>
                        </div>

                        <p class="text-xs text-blue-200/90 font-medium">
                            {{ $franchise->tagline ?: 'Multi-Branch Accredited Learning & Vocational Institute' }}
                        </p>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300 pt-1">
                            @if($franchise->city)
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $franchise->city }}, {{ $franchise->state ?: $franchise->country }}</span>
                                </span>
                            @endif
                            @if($franchise->email)
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span>{{ $franchise->email }}</span>
                                </span>
                            @endif
                            @if($franchise->phone)
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>{{ $franchise->phone }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription & Quota Gauges -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 min-w-[300px]">
                    <div class="p-3.5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-300 block tracking-wider">Subscription Tier</span>
                        <span class="text-sm font-black text-amber-300 block mt-0.5 truncate">{{ $franchise->subscriptionPlan?->name ?? 'Enterprise Network' }}</span>
                        <span class="text-[10px] text-slate-400">Expires: {{ $franchise->plan_expires_at ? $franchise->plan_expires_at->format('M Y') : 'Active' }}</span>
                    </div>

                    <div class="p-3.5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 text-center">
                        <span class="text-[10px] uppercase font-bold text-blue-300 block tracking-wider">Branch Quota</span>
                        <div class="flex items-baseline justify-center space-x-1 mt-0.5">
                            <span class="text-lg font-black text-white">{{ $totalBranches }}</span>
                            <span class="text-xs text-slate-400">/ {{ $franchise->max_branches }}</span>
                        </div>
                        <div class="w-full bg-slate-700/60 rounded-full h-1.5 mt-1 overflow-hidden">
                            <div class="bg-blue-400 h-1.5 rounded-full" style="width: {{ min(100, ($totalBranches / max(1, $franchise->max_branches)) * 100) }}%"></div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 text-center col-span-2 sm:col-span-1">
                        <span class="text-[10px] uppercase font-bold text-blue-300 block tracking-wider">Student Capacity</span>
                        <div class="flex items-baseline justify-center space-x-1 mt-0.5">
                            <span class="text-lg font-black text-emerald-400">{{ $totalStudents }}</span>
                            <span class="text-xs text-slate-400">/ {{ $franchise->max_students }}</span>
                        </div>
                        <div class="w-full bg-slate-700/60 rounded-full h-1.5 mt-1 overflow-hidden">
                            <div class="bg-emerald-400 h-1.5 rounded-full" style="width: {{ min(100, ($totalStudents / max(1, $franchise->max_students)) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instant Metrics Strip -->
            <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-2 sm:grid-cols-5 gap-3 text-center">
                <div class="p-2.5 bg-white/5 rounded-xl border border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Students</span>
                    <span class="text-xl font-extrabold text-white mt-0.5 block">{{ $totalStudents }}</span>
                </div>
                <div class="p-2.5 bg-white/5 rounded-xl border border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Campuses / Branches</span>
                    <span class="text-xl font-extrabold text-blue-300 mt-0.5 block">{{ $totalBranches }}</span>
                </div>
                <div class="p-2.5 bg-white/5 rounded-xl border border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Accredited Courses</span>
                    <span class="text-xl font-extrabold text-white mt-0.5 block">{{ $totalCourses }}</span>
                </div>
                <div class="p-2.5 bg-white/5 rounded-xl border border-white/5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Running Batches</span>
                    <span class="text-xl font-extrabold text-white mt-0.5 block">{{ $totalBatches }}</span>
                </div>
                <div class="p-2.5 bg-white/5 rounded-xl border border-white/5 col-span-2 sm:col-span-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Issued Certificates</span>
                    <span class="text-xl font-extrabold text-amber-300 mt-0.5 block">{{ $totalCertificates }}</span>
                </div>
            </div>
        </div>

        <!-- Official Institute Settings & Branding Form -->
        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isFranchiseOwner() || auth()->user()->isBranchAdmin())
                <div class="flex justify-start pt-2">
                    <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle" color="primary">
                        Save Institute Profile & Branding
                    </x-filament::button>
                </div>
            @endif
        </x-filament-panels::form>

        @php
            $studentName = $sampleStudent?->full_name ?? 'Aarav Sharma';
            $studentCode = $sampleStudent?->student_id_code ?? 'APEX-STU-001';
            $admNo = $sampleStudent?->admission_number ?? 'ADM-2026-0042';
            $courseName = $sampleCertificate?->course?->name ?? ($sampleStudent?->enrollments?->first()?->course?->name ?? 'Full Stack Web Development & AI Architecture');
            $branchName = $sampleStudent?->branch?->name ?? ($franchise->branches->first()?->name ?? 'Downtown Campus');
            $receiptNo = $samplePayment?->receipt_number ?? 'REC-2026-0091';
            $receiptDate = $samplePayment?->payment_date ? $samplePayment->payment_date->format('d M, Y') : date('d M, Y');
            $paidAmount = $samplePayment?->amount ?? 15000.00;
        @endphp

        <!-- ========================================================================= -->
        <!-- LIVE DOCUMENT & BRAND PROOF STUDIO (100% Light & Dark Mode Compatible)    -->
        <!-- ========================================================================= -->
        <div x-data="{ activeProofTab: 'certificate' }">
            <x-filament::section
                icon="heroicon-o-eye"
                heading="Live Document & Brand Proof Studio"
                description="Real-time visual proof of how your official Logo, Stamp Seal, and Director Signature appear on student credentials, identity cards, and fee receipts."
            >
                <x-slot name="headerEnd">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-filament::badge
                            :color="$franchise->logo ? 'success' : 'warning'"
                            :icon="$franchise->logo ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle'"
                            size="sm"
                        >
                            Logo: {{ $franchise->logo ? 'Active' : 'Missing' }}
                        </x-filament::badge>

                        <x-filament::badge
                            :color="$franchise->stamp ? 'success' : 'warning'"
                            :icon="$franchise->stamp ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle'"
                            size="sm"
                        >
                            Stamp Seal: {{ $franchise->stamp ? 'Active' : 'Missing' }}
                        </x-filament::badge>

                        <x-filament::badge
                            :color="$franchise->signature ? 'success' : 'warning'"
                            :icon="$franchise->signature ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle'"
                            size="sm"
                        >
                            Signature: {{ $franchise->signature ? 'Active' : 'Missing' }}
                        </x-filament::badge>

                        @if($franchise->tax_number)
                            <x-filament::badge color="info" size="sm">
                                GSTIN: {{ $franchise->tax_number }}
                            </x-filament::badge>
                        @endif
                    </div>
                </x-slot>

                <!-- Navigation Tabs & Actions -->
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-200 dark:border-white/10 pb-3">
                        <x-filament::tabs contained>
                            <x-filament::tabs.item
                                icon="heroicon-o-academic-cap"
                                alpine-active="activeProofTab === 'certificate'"
                                @click="activeProofTab = 'certificate'"
                            >
                                Official Certificate Proof
                            </x-filament::tabs.item>

                            <x-filament::tabs.item
                                icon="heroicon-o-identification"
                                alpine-active="activeProofTab === 'idcard'"
                                @click="activeProofTab = 'idcard'"
                            >
                                Student PVC ID Card Proof
                            </x-filament::tabs.item>

                            <x-filament::tabs.item
                                icon="heroicon-o-receipt-percent"
                                alpine-active="activeProofTab === 'receipt'"
                                @click="activeProofTab = 'receipt'"
                            >
                                GST Fee Receipt Proof
                            </x-filament::tabs.item>
                        </x-filament::tabs>

                        <div>
                            <template x-if="activeProofTab === 'certificate'">
                                @if($sampleCertificate)
                                    <x-filament::button
                                        tag="a"
                                        href="{{ route('certificate.print', ['code' => $sampleCertificate->verification_code]) }}"
                                        target="_blank"
                                        size="xs"
                                        color="gray"
                                        icon="heroicon-m-printer"
                                    >
                                        Open Real Print View
                                    </x-filament::button>
                                @endif
                            </template>

                            <template x-if="activeProofTab === 'idcard'">
                                @if($sampleStudent)
                                    <x-filament::button
                                        tag="a"
                                        href="{{ route('student.id-card.print', ['id' => $sampleStudent->id]) }}"
                                        target="_blank"
                                        size="xs"
                                        color="gray"
                                        icon="heroicon-m-printer"
                                    >
                                        Open Real Print View
                                    </x-filament::button>
                                @endif
                            </template>

                            <template x-if="activeProofTab === 'receipt'">
                                @if($samplePayment)
                                    <x-filament::button
                                        tag="a"
                                        href="{{ route('receipt.print', ['receiptNumber' => $samplePayment->receipt_number]) }}"
                                        target="_blank"
                                        size="xs"
                                        color="gray"
                                        icon="heroicon-m-printer"
                                    >
                                        Open Real Print View
                                    </x-filament::button>
                                @endif
                            </template>
                        </div>
                    </div>

                    <!-- Tray for Document Mockup Sheets -->
                    <div class="rounded-2xl p-4 sm:p-6 bg-gray-100 dark:bg-gray-950/80 border border-gray-200 dark:border-white/10">

                        <!-- TAB 1: CERTIFICATE -->
                        <div x-show="activeProofTab === 'certificate'" class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <span>Document Proportion: A4 Landscape (297mm × 210mm)</span>
                                <span class="font-semibold text-primary-600 dark:text-primary-400">Embossed Gold Seal &amp; Dynamic QR Enabled</span>
                            </div>

                            <!-- White Paper Sheet with Guaranteed High Contrast -->
                            <div style="background-color: #ffffff !important; color: #0f172a !important; border-radius: 16px; border: 4px double #1e3a8a; position: relative; overflow: hidden; padding: 28px 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);">
                                <!-- Inner Gold Border -->
                                <div style="position: absolute; inset: 10px; border: 2px solid rgba(217, 119, 6, 0.6); border-radius: 10px; pointer-events: none;"></div>
                                <div style="position: absolute; inset: 14px; border: 1px solid rgba(30, 58, 138, 0.2); border-radius: 8px; pointer-events: none;"></div>

                                <!-- Corner Ornaments -->
                                <div style="position: absolute; top: 14px; left: 14px; width: 24px; height: 24px; border-top: 3px solid #b45309; border-left: 3px solid #b45309; pointer-events: none;"></div>
                                <div style="position: absolute; top: 14px; right: 14px; width: 24px; height: 24px; border-top: 3px solid #b45309; border-right: 3px solid #b45309; pointer-events: none;"></div>
                                <div style="position: absolute; bottom: 14px; left: 14px; width: 24px; height: 24px; border-bottom: 3px solid #b45309; border-left: 3px solid #b45309; pointer-events: none;"></div>
                                <div style="position: absolute; bottom: 14px; right: 14px; width: 24px; height: 24px; border-bottom: 3px solid #b45309; border-right: 3px solid #b45309; pointer-events: none;"></div>

                                <!-- Certificate Header -->
                                <div style="position: relative; z-index: 10; text-align: center; margin-bottom: 18px;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 14px; margin-bottom: 12px;">
                                        @if($franchise->logo)
                                            <img src="{{ asset('storage/' . $franchise->logo) }}" alt="Logo" style="width: 56px; height: 56px; max-width: 56px; max-height: 56px; object-fit: contain; border-radius: 10px; border: 1px solid #fcd34d; background: #ffffff; padding: 4px; display: inline-block;">
                                        @else
                                            <div style="width: 52px; height: 52px; border-radius: 50%; background: #1e3a8a; color: #fbbf24; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 22px; border: 2px solid #fbbf24; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                ★
                                            </div>
                                        @endif

                                        <div style="text-align: left;">
                                            <div style="font-size: 22px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; color: #0f172a; font-family: serif; line-height: 1.2;">
                                                {{ $franchise->name }}
                                            </div>
                                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 700; color: #b45309; margin-top: 2px;">
                                                {{ $franchise->tagline ?: ($branchName . ' • Accredited Vocational Training Network') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div style="font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: #0f172a; font-family: serif;">
                                            Certificate of Completion
                                        </div>
                                        <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.2em; color: #64748b; font-weight: 700; margin-top: 2px;">
                                            This is proudly conferred upon
                                        </div>
                                    </div>
                                </div>

                                <!-- Recipient & Course Details -->
                                <div style="position: relative; z-index: 10; text-align: center; margin: 20px 0;">
                                    <div style="font-size: 28px; font-weight: 800; color: #1e3a8a; font-family: serif; text-decoration: underline; text-decoration-color: #fbbf24; text-underline-offset: 6px;">
                                        {{ $studentName }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 8px;">
                                        Student Identification ID: <strong style="color: #0f172a; font-family: monospace;">{{ $studentCode }}</strong> &bull; Adm: <strong style="color: #0f172a; font-family: monospace;">{{ $admNo }}</strong>
                                    </div>

                                    <p style="max-width: 580px; margin: 10px auto 0; font-size: 12px; color: #334155; line-height: 1.6;">
                                        For successfully fulfilling all prescribed academic curriculum and practical laboratory requirements for the credential program of
                                    </p>

                                    <div style="font-size: 17px; font-weight: 700; color: #0f172a; font-family: serif; margin-top: 6px;">
                                        {{ $courseName }}
                                    </div>
                                    <div style="font-size: 10px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;">
                                        Completed with <strong style="color: #1e3a8a;">Grade A (Distinction)</strong> &bull; Issue Code: <span style="color: #0f172a; font-family: monospace;">CERT-2026-APEX-098</span>
                                    </div>
                                </div>

                                <!-- Certificate Footer: QR + Official Seal + Signatures -->
                                <div style="position: relative; z-index: 10; padding-top: 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; border-top: 1px solid #e2e8f0; gap: 16px;">
                                    <!-- Left: QR Code -->
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="padding: 4px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center;">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=52x52&data={{ urlencode(url('/verify-certificate')) }}" alt="Verification QR" style="width: 48px; height: 48px; display: block;">
                                            <span style="font-size: 7px; color: #94a3b8; display: block; margin-top: 2px;">Scan to Verify</span>
                                        </div>
                                        <div style="font-size: 10px; color: #64748b; line-height: 1.4;">
                                            <div><strong style="color: #0f172a;">Date Issued:</strong> {{ date('d/m/Y') }}</div>
                                            <div><strong style="color: #0f172a;">Institute Code:</strong> <span style="color: #1e3a8a; font-family: monospace; font-weight: bold;">{{ $franchise->code }}</span></div>
                                            <div style="color: #059669; font-weight: bold;">🔒 Digitally Cryptographically Verified</div>
                                        </div>
                                    </div>

                                    <!-- Center: Official Stamp Seal -->
                                    <div style="text-align: center;">
                                        @if($franchise->stamp)
                                            <div style="width: 76px; height: 76px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                <img src="{{ asset('storage/' . $franchise->stamp) }}" alt="Official Stamp" style="width: 72px; height: 72px; max-width: 72px; max-height: 72px; object-fit: contain; transform: rotate(-6deg); display: block;">
                                            </div>
                                            <span style="font-size: 8px; text-transform: uppercase; font-weight: 800; color: #0f172a; display: block; margin-top: 2px;">Official Seal</span>
                                        @else
                                            <div style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #fbbf24, #b45309); color: #0f172a; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 2px solid #ffffff;">
                                                <span style="font-size: 14px;">★</span>
                                                <span style="font-size: 7px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em;">SEAL</span>
                                            </div>
                                            <span style="font-size: 8px; color: #d97706; font-weight: bold; display: block; margin-top: 4px;">(Upload stamp above)</span>
                                        @endif
                                    </div>

                                    <!-- Right: Authorized Director Signature -->
                                    <div style="text-align: center; min-width: 140px;">
                                        <div style="height: 42px; border-bottom: 1px solid #94a3b8; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 4px;">
                                            @if($franchise->signature)
                                                <img src="{{ asset('storage/' . $franchise->signature) }}" alt="Director Signature" style="height: 36px; max-width: 130px; object-fit: contain; display: block;">
                                            @else
                                                <span style="font-family: serif; font-style: italic; font-size: 15px; color: #1e3a8a;">Authorized Signatory</span>
                                            @endif
                                        </div>
                                        <span style="font-size: 10px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-top: 4px;">Academic Director</span>
                                        <span style="font-size: 8px; color: #64748b; display: block;">{{ $franchise->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: STUDENT PVC ID CARD -->
                        <div x-show="activeProofTab === 'idcard'" class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <span>Form Factor: CR80 PVC Standard (85.6mm × 54mm)</span>
                                <span class="font-semibold text-primary-600 dark:text-primary-400">Dual-Sided Layout</span>
                            </div>

                            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 24px; padding: 20px 0;">
                                <!-- FRONT SIDE -->
                                <div style="width: 320px; height: 204px; background: #ffffff !important; color: #0f172a !important; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 6px 12px -2px rgba(0,0,0,0.12);">
                                    <!-- Top Band -->
                                    <div style="background: linear-gradient(135deg, #1d4ed8, #1e3a8a, #312e81); padding: 6px 12px; display: flex; align-items: center; justify-content: space-between; color: #ffffff;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            @if($franchise->logo)
                                                <img src="{{ asset('storage/' . $franchise->logo) }}" alt="Logo" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px; object-fit: contain; border-radius: 4px; background: rgba(255,255,255,0.2); padding: 2px;">
                                            @else
                                                <div style="width: 24px; height: 24px; border-radius: 50%; background: #ffffff; color: #1d4ed8; font-weight: 900; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                                    {{ strtoupper(substr($franchise->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1;">{{ $franchise->name }}</div>
                                                <div style="font-size: 7.5px; color: #bfdbfe; font-weight: 500; margin-top: 1px; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $franchise->tagline ?: ($branchName . ' • Campus') }}</div>
                                            </div>
                                        </div>
                                        <span style="font-size: 6.5px; font-weight: 800; text-transform: uppercase; background: rgba(59,130,246,0.3); padding: 2px 6px; border-radius: 4px; color: #dbeafe; letter-spacing: 0.05em;">STUDENT ID</span>
                                    </div>

                                    <!-- Body Content -->
                                    <div style="padding: 6px 12px; display: flex; gap: 10px; align-items: center; flex: 1;">
                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                            <div style="width: 58px; height: 70px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f1f5f9; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                <div style="font-size: 18px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">
                                                    {{ strtoupper(substr($studentName, 0, 2)) }}
                                                </div>
                                            </div>
                                            <span style="font-size: 7px; font-family: monospace; font-weight: 700; color: #475569; margin-top: 4px; background: #f1f5f9; padding: 1px 4px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                                {{ $studentCode }}
                                            </span>
                                        </div>

                                        <div style="flex: 1; font-size: 8px; line-height: 1.3;">
                                            <div>
                                                <div style="font-size: 11px; font-weight: 800; color: #0f172a; line-height: 1.2;">{{ $studentName }}</div>
                                                <div style="font-size: 7.5px; color: #2563eb; font-weight: 600;">Adm No: <span style="font-family: monospace; color: #334155;">{{ $admNo }}</span></div>
                                            </div>
                                            <div style="padding-top: 4px; margin-top: 4px; border-top: 1px solid #f1f5f9; line-height: 1.4;">
                                                <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Course:</span> <strong style="color: #0f172a; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $courseName }}</strong></div>
                                                <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Campus:</span> <strong style="color: #334155;">{{ $branchName }}</strong></div>
                                                <div style="display: flex; justify-content: space-between;"><span style="color: #64748b;">Valid Thru:</span> <span style="font-family: monospace; color: #334155;">{{ date('Y') }} - {{ date('Y') + 1 }}</span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Strip -->
                                    <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 3px 12px; display: flex; align-items: center; justify-content: space-between; font-size: 7px;">
                                        <span style="font-weight: 700; color: #059669; text-transform: uppercase;">● Status: Active Student</span>
                                        <span style="font-family: monospace; color: #94a3b8;">Accredited ID</span>
                                    </div>
                                </div>

                                <!-- BACK SIDE -->
                                <div style="width: 320px; height: 204px; background: #ffffff !important; color: #0f172a !important; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 6px 12px -2px rgba(0,0,0,0.12);">
                                    <div style="background: #1e293b; padding: 6px 12px; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                                        <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Campus Terms &amp; Instructions</span>
                                        <span style="font-size: 7px; color: #94a3b8; font-family: monospace;">CR80-BACK</span>
                                    </div>

                                    <div style="padding: 8px 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; font-size: 7px; color: #475569; line-height: 1.3;">
                                        <ul style="padding-left: 12px; list-style-type: disc; margin: 0;">
                                            <li>Non-transferable identity credential for campus and labs.</li>
                                            <li>Loss must be reported to the academic branch desk immediately.</li>
                                            <li>Minimum 75% attendance mandatory for final exams.</li>
                                        </ul>

                                        <div style="background: #f8fafc; padding: 6px 8px; border-radius: 6px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
                                            <div>
                                                <div style="font-size: 6.5px; text-transform: uppercase; font-weight: 700; color: #94a3b8;">Institute Helpline:</div>
                                                <div style="font-weight: 800; color: #0f172a; font-size: 7.5px;">{{ $franchise->phone ?: '+91 98765 43210' }}</div>
                                                <div style="font-size: 6.5px; color: #64748b; max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $franchise->city }}, {{ $franchise->email }}</div>
                                            </div>
                                            <div style="width: 26px; height: 26px; background: #ffffff; border: 1px solid #cbd5e1; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                                <svg style="width: 100%; height: 100%; color: #1e293b;" viewBox="0 0 24 24" fill="currentColor"><path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14-2h2v2h-2v-2zm-4 0h2v4h-2v-4zm4 4h2v2h-2v-2zm-2 2h2v2h-2v-2zm-2-2h2v2h-2v-2zm4 2h2v2h-2v-2z"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Back Footer: Signatures -->
                                    <div style="border-top: 1px solid #e2e8f0; padding: 4px 12px; display: flex; align-items: center; justify-content: space-between; font-size: 6.5px; color: #94a3b8; background: #f8fafc;">
                                        <div>Student Signature</div>
                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                            @if($franchise->signature)
                                                <img src="{{ asset('storage/' . $franchise->signature) }}" alt="Signature" style="height: 14px; max-width: 60px; object-fit: contain; display: block;">
                                            @endif
                                            <span style="font-weight: 700; color: #334155;">Authorized Signatory</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: FEE RECEIPT -->
                        <div x-show="activeProofTab === 'receipt'" class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                                <span>Document Proportion: Tax Invoice &amp; Official Fee Voucher</span>
                                <span class="font-semibold text-primary-600 dark:text-primary-400">Audit Compliant &amp; Seal Validated</span>
                            </div>

                            <div style="background-color: #ffffff !important; color: #0f172a !important; border-radius: 16px; border: 1px solid #cbd5e1; max-width: 640px; margin: 0 auto; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12);">
                                <!-- Header -->
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                                        @if($franchise->logo)
                                            <img src="{{ asset('storage/' . $franchise->logo) }}" alt="Logo" style="width: 48px; height: 48px; max-width: 48px; max-height: 48px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; padding: 2px; background: #ffffff;">
                                        @endif
                                        <div>
                                            <div style="font-size: 18px; font-weight: 900; color: #1e3a8a; line-height: 1.2;">{{ $franchise->name }}</div>
                                            @if($franchise->tagline)
                                                <div style="font-size: 11px; color: #2563eb; font-weight: 600;">{{ $franchise->tagline }}</div>
                                            @endif
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $branchName }} &bull; {{ $franchise->address ?: ($franchise->city . ', ' . $franchise->state) }}</div>
                                            @if($franchise->tax_number)
                                                <div style="font-size: 10px; color: #475569; font-family: monospace; margin-top: 2px;">GSTIN: <strong style="color: #0f172a;">{{ $franchise->tax_number }}</strong></div>
                                            @endif
                                        </div>
                                    </div>

                                    <div style="text-align: right;">
                                        <span style="display: inline-block; padding: 2px 8px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 10px; font-weight: 800; text-transform: uppercase; border-radius: 4px; margin-bottom: 4px;">
                                            Fee Receipt
                                        </span>
                                        <div style="font-size: 11px; font-family: monospace; font-weight: bold; color: #0f172a;">#{{ $receiptNo }}</div>
                                        <div style="font-size: 10px; color: #64748b;">Date: {{ $receiptDate }}</div>
                                    </div>
                                </div>

                                <!-- Meta Details -->
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 14px 0; border-bottom: 1px solid #e2e8f0; font-size: 11px;">
                                    <div>
                                        <span style="color: #94a3b8; text-transform: uppercase; font-size: 9px; font-weight: 800; display: block;">Billed To (Student)</span>
                                        <strong style="font-size: 13px; color: #0f172a; display: block; margin-top: 2px;">{{ $studentName }}</strong>
                                        <span style="color: #64748b; font-family: monospace;">Roll: {{ $studentCode }}</span>
                                    </div>
                                    <div style="text-align: right;">
                                        <span style="color: #94a3b8; text-transform: uppercase; font-size: 9px; font-weight: 800; display: block;">Enrolled Program</span>
                                        <strong style="font-size: 12px; color: #0f172a; display: block; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $courseName }}</strong>
                                        <span style="color: #64748b; font-size: 10px;">Method: UPI / Direct Counter Settlement</span>
                                    </div>
                                </div>

                                <!-- Amount Table -->
                                <div style="padding: 14px 0; border-bottom: 1px solid #e2e8f0; font-size: 11px;">
                                    <div style="display: flex; justify-content: space-between; padding-bottom: 6px; color: #94a3b8; font-weight: 800; text-transform: uppercase; font-size: 9px;">
                                        <span>Description</span>
                                        <span>Amount (INR)</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-top: 1px solid #f1f5f9; font-weight: 600; color: #334155;">
                                        <span>Course Tuition Fee Installment (#1)</span>
                                        <span style="font-family: monospace; font-weight: bold; color: #0f172a;">₹{{ number_format($paidAmount, 2) }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px solid #f1f5f9; font-size: 13px; font-weight: 800; color: #1e3a8a;">
                                        <span>Total Settlement Paid:</span>
                                        <span style="font-family: monospace;">₹{{ number_format($paidAmount, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Receipt Footer: Stamp & Signatures -->
                                <div style="padding-top: 16px; display: flex; align-items: flex-end; justify-content: space-between; font-size: 11px; color: #64748b;">
                                    <div>
                                        <div style="font-weight: 700; color: #334155;">Official Clearance Note:</div>
                                        <div style="font-size: 9.5px; color: #94a3b8; max-width: 260px; margin-top: 2px;">
                                            Fees once paid are non-refundable. Retain this digital receipt for semester examination hall permits.
                                        </div>
                                        <div style="font-size: 9px; color: #94a3b8; font-family: monospace; margin-top: 4px;">Cashier: Accounts Billing Desk</div>
                                    </div>

                                    <div style="display: flex; align-items: flex-end; gap: 16px;">
                                        @if($franchise->stamp)
                                            <div style="text-align: center;">
                                                <img src="{{ asset('storage/' . $franchise->stamp) }}" alt="Official Stamp" style="width: 52px; height: 52px; max-width: 52px; max-height: 52px; object-fit: contain; transform: rotate(-4deg); opacity: 0.9; margin: 0 auto 4px;">
                                                <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; color: #94a3b8; display: block;">Official Seal</span>
                                            </div>
                                        @endif

                                        <div style="text-align: center;">
                                            <div style="width: 110px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; min-height: 36px; display: flex; align-items: flex-end; justify-content: center;">
                                                @if($franchise->signature)
                                                    <img src="{{ asset('storage/' . $franchise->signature) }}" alt="Authorized Signature" style="height: 30px; max-width: 100px; object-fit: contain; display: block;">
                                                @else
                                                    <span style="font-family: monospace; font-size: 9px; font-weight: bold; color: #475569;">[Authorized Signatory]</span>
                                                @endif
                                            </div>
                                            <span style="font-size: 8.5px; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-top: 4px;">Authorized Cashier</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Affiliated Campus Network Summary (Theme-Aware) -->
        <x-filament::section
            icon="heroicon-o-building-office"
            heading="Affiliated Campus Network"
            description="All registered branches operating under {{ $franchise->name }}."
        >
            <x-slot name="headerEnd">
                <x-filament::button
                    tag="a"
                    href="{{ url('/app/' . $franchise->slug . '/branches') }}"
                    size="xs"
                    color="primary"
                    icon="heroicon-m-arrow-right"
                    icon-position="after"
                >
                    Manage Branches
                </x-filament::button>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($franchise->branches as $branch)
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 space-y-2 text-xs">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="font-mono text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase">{{ $branch->code }}</span>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ $branch->name }}</h4>
                            </div>
                            <x-filament::badge :color="$branch->status === 'active' ? 'success' : 'gray'" size="sm">
                                {{ ucfirst($branch->status) }}
                            </x-filament::badge>
                        </div>

                        <div class="space-y-1 text-gray-600 dark:text-gray-300 text-[11px] pt-1">
                            @if($branch->city)
                                <p class="flex items-center space-x-1.5">
                                    <span>📍</span> <span>{{ $branch->city }}</span>
                                </p>
                            @endif
                            @if($branch->email)
                                <p class="flex items-center space-x-1.5 truncate">
                                    <span>✉️</span> <span>{{ $branch->email }}</span>
                                </p>
                            @endif
                            @if($branch->phone)
                                <p class="flex items-center space-x-1.5">
                                    <span>📞</span> <span>{{ $branch->phone }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 italic py-2">No branches configured yet.</p>
                @endforelse
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>

