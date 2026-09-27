<x-filament-panels::page.simple>
    <div class="mb-4">
        <!-- Interactive Role Switcher Selector -->
        <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2.5 text-center flex items-center justify-center gap-1.5">
            <span class="inline-block w-2 h-2 rounded-full bg-primary-500 animate-pulse"></span>
            <span>Select Your Institute Workspace Role</span>
        </div>
        
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 mb-3.5">
            <!-- 1. Franchise Owner -->
            <button 
                type="button" 
                wire:click="selectRole('franchise_owner')"
                class="flex flex-col items-center justify-center p-2.5 rounded-xl border text-xs font-semibold transition-all duration-150 {{ $selectedRole === 'franchise_owner' ? 'bg-amber-500/10 border-amber-500 text-amber-800 dark:text-amber-300 ring-2 ring-amber-500/40 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                <span class="text-xl mb-1">👑</span>
                <span class="leading-tight text-center">Franchise Owner</span>
            </button>

            <!-- 2. Branch Admin -->
            <button 
                type="button" 
                wire:click="selectRole('branch_admin')"
                class="flex flex-col items-center justify-center p-2.5 rounded-xl border text-xs font-semibold transition-all duration-150 {{ $selectedRole === 'branch_admin' ? 'bg-blue-500/10 border-blue-500 text-blue-800 dark:text-blue-300 ring-2 ring-blue-500/40 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                <span class="text-xl mb-1">🏢</span>
                <span class="leading-tight text-center">Branch Admin</span>
            </button>

            <!-- 3. Faculty / Trainer -->
            <button 
                type="button" 
                wire:click="selectRole('trainer')"
                class="flex flex-col items-center justify-center p-2.5 rounded-xl border text-xs font-semibold transition-all duration-150 {{ $selectedRole === 'trainer' ? 'bg-emerald-500/10 border-emerald-500 text-emerald-800 dark:text-emerald-300 ring-2 ring-emerald-500/40 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                <span class="text-xl mb-1">🧑‍🏫</span>
                <span class="leading-tight text-center">Faculty / Trainer</span>
            </button>

            <!-- 4. Accountant -->
            <button 
                type="button" 
                wire:click="selectRole('accountant')"
                class="flex flex-col items-center justify-center p-2.5 rounded-xl border text-xs font-semibold transition-all duration-150 {{ $selectedRole === 'accountant' ? 'bg-indigo-500/10 border-indigo-500 text-indigo-800 dark:text-indigo-300 ring-2 ring-indigo-500/40 shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800' }}"
            >
                <span class="text-xl mb-1">💼</span>
                <span class="leading-tight text-center">Accountant</span>
            </button>
        </div>

        <!-- Role Workspace Description & Quick-Fill Card -->
        <div class="rounded-xl p-3 text-xs border {{ match($selectedRole) {
            'franchise_owner' => 'bg-amber-500/10 border-amber-500/20 text-amber-950 dark:text-amber-200',
            'branch_admin' => 'bg-blue-500/10 border-blue-500/20 text-blue-950 dark:text-blue-200',
            'trainer' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-950 dark:text-emerald-200',
            'accountant' => 'bg-indigo-500/10 border-indigo-500/20 text-indigo-950 dark:text-indigo-200',
            default => 'bg-gray-50 border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300'
        } }}">
            <div class="flex items-center justify-between mb-1.5">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                    <span>
                        {{ match($selectedRole) {
                            'franchise_owner' => '👑 Executive Director Workspace',
                            'branch_admin' => '🏢 Campus Branch Head Workspace',
                            'trainer' => '🧑‍🏫 Faculty Academic Desk',
                            'accountant' => '💼 Accounts & Billing Desk',
                            default => 'Staff Workspace'
                        } }}
                    </span>
                </span>
                <button 
                    type="button" 
                    wire:click="fillDemoCredentials('{{ $selectedRole }}')" 
                    class="font-semibold underline hover:no-underline text-primary-600 dark:text-primary-400 flex items-center gap-1 text-[11px]"
                >
                    <span>⚡ Quick Fill Demo</span>
                </button>
            </div>
            <p class="leading-relaxed opacity-90 text-[11px]">
                {{ match($selectedRole) {
                    'franchise_owner' => 'Full executive control across all campus branches, revenue books, master course catalog, trainer hiring, and student enrollment records.',
                    'branch_admin' => 'Campus operations desk: Student admissions, local fee receipts, batch schedules, attendance logs, and branch trainer assignments.',
                    'trainer' => 'Academic instruction desk: Assigned batch rosters, daily attendance marking, syllabus & study material distribution, and exam scoring (Financials & Settings hidden).',
                    'accountant' => 'Treasury & billing desk: Fee invoice generation, instant receipt ledger, installment dues tracking, and payment collection reconciliations.',
                    default => 'Sign in with your institute staff credentials.'
                } }}
            </p>
        </div>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    <!-- Quick Footer Navigation -->
    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-800 text-center text-xs text-gray-500 dark:text-gray-400 flex items-center justify-center gap-4">
        <a href="{{ route('student.login') }}" class="hover:text-primary-500 hover:underline">🎓 Student Portal Login &rarr;</a>
        <span>&bull;</span>
        <a href="{{ url('/') }}" class="hover:text-primary-500 hover:underline">🏠 Public Website</a>
    </div>
</x-filament-panels::page.simple>
