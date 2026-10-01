@php
    $user = auth()->user()?->fresh() ?? auth()->user();
    $roleLabels = [
        'super_admin' => '👑 Super Administrator (Central Hub)',
        'franchise_owner' => '🏛️ Franchise Owner & Executive Director',
        'branch_admin' => '🏢 Branch Center Head / Administrator',
        'trainer' => '👨‍🏫 Certified Faculty / Lead Trainer',
        'accountant' => '💼 Financial Accounts Officer',
        'student' => '🎓 Registered Student',
    ];
    $roleName = $roleLabels[$user->role ?? ''] ?? ucwords(str_replace('_', ' ', $user->role ?? 'Staff Member'));
    $franchise = $user->franchise ?? (\Filament\Facades\Filament::hasTenancy() ? \Filament\Facades\Filament::getTenant() : null);
@endphp

<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Staff Account Profile Hero Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 p-6 sm:p-8 text-white shadow-xl border border-blue-900/40">
            <!-- Decorative background glows -->
            <div class="absolute -right-16 -top-16 w-60 h-60 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-60 h-60 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center space-x-5">
                    @if($user->avatar)
                        <img src="{{ $user->getFilamentAvatarUrl() }}" alt="{{ $user->name }}" style="width: 80px; height: 80px; min-width: 80px; min-height: 80px; max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 1rem;" class="border-2 border-white/30 shadow-lg bg-white/10">
                    @else
                        <div style="width: 80px; height: 80px; min-width: 80px; min-height: 80px;" class="rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-2xl font-black text-white shadow-lg border-2 border-white/20">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-2xl font-black text-white tracking-tight">{{ $user->name }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </div>

                        <p class="text-xs font-semibold text-blue-300">
                            {{ $user->designation ?: $roleName }}
                        </p>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300 pt-1">
                            <span class="flex items-center space-x-1.5">
                                <span>✉️</span> <span>{{ $user->email }}</span>
                            </span>
                            @if($user->phone)
                                <span class="flex items-center space-x-1.5">
                                    <span>📞</span> <span>{{ $user->phone }}</span>
                                </span>
                            @endif
                            @if($franchise)
                                <span class="flex items-center space-x-1.5">
                                    <span>🏛️</span> <span>{{ $franchise->name }}</span>
                                </span>
                            @endif
                            @if($user->branch)
                                <span class="flex items-center space-x-1.5">
                                    <span>📍</span> <span>{{ $user->branch->name }}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Role Badge Pill -->
                <div class="text-right">
                    <span class="inline-block px-3.5 py-1.5 rounded-xl bg-white/10 border border-white/10 text-xs font-bold text-blue-200 shadow-sm">
                        {{ $roleName }}
                    </span>
                </div>
            </div>

            @if($franchise)
                <!-- Quick Link to Institute Profile Banner -->
                <div class="relative z-10 mt-6 pt-5 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 text-xs">
                        <span class="text-2xl">🏛️</span>
                        <div>
                            <strong class="text-white block font-bold">Managing {{ $franchise->name }}?</strong>
                            <span class="text-blue-200 text-[11px]">Configure official institute logo, seal stamp, director signature, contact info, and tax GSTIN.</span>
                        </div>
                    </div>
                    <a href="{{ url('/app/' . $franchise->slug . '/institute-profile') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition shadow-md whitespace-nowrap">
                        <span>Institute Profile & Branding &rarr;</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Form with full panels layout -->
        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}

            <div class="flex justify-start pt-2">
                <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle" color="primary">
                    Save Profile Changes
                </x-filament::button>
            </div>
        </x-filament-panels::form>

    </div>
</x-filament-panels::page>
