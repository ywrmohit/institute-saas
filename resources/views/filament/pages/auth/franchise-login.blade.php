<x-filament-panels::page.simple>
    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    <!-- Multi-Portal Footer Navigation -->
    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 text-center text-xs text-gray-500 dark:text-gray-400 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('student.login') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline flex items-center gap-1">
            <span>🎓</span>
            <span>Student Portal</span>
        </a>
        <span class="text-gray-300 dark:text-gray-700">&bull;</span>
        <a href="{{ url('/admin/login') }}" class="hover:text-gray-700 dark:hover:text-gray-300 hover:underline">
            <span>🛡️ Super Admin</span>
        </a>
        <span class="text-gray-300 dark:text-gray-700">&bull;</span>
        <a href="{{ url('/') }}" class="hover:text-gray-700 dark:hover:text-gray-300 hover:underline">
            <span>🏠 Public Site</span>
        </a>
    </div>

    @if (app()->environment('local'))
        <!-- Developer Quick Fill (Only visible in local development environment) -->
        <details class="mt-4 pt-3 border-t border-dashed border-gray-200 dark:border-gray-800 text-[11px] text-gray-400">
            <summary class="cursor-pointer hover:text-gray-600 dark:hover:text-gray-300 font-medium text-center select-none">
                🛠️ Quick Fill Test Accounts (Local Dev Only)
            </summary>
            <div class="grid grid-cols-2 gap-1.5 mt-2.5">
                <button 
                    type="button" 
                    wire:click="fillDemoCredentials('franchise_owner')" 
                    class="px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-left truncate flex items-center gap-1"
                >
                    <span>👑</span>
                    <span class="truncate">Owner (Apex)</span>
                </button>
                <button 
                    type="button" 
                    wire:click="fillDemoCredentials('branch_admin')" 
                    class="px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-left truncate flex items-center gap-1"
                >
                    <span>🏢</span>
                    <span class="truncate">Branch Admin</span>
                </button>
                <button 
                    type="button" 
                    wire:click="fillDemoCredentials('trainer')" 
                    class="px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-left truncate flex items-center gap-1"
                >
                    <span>🧑‍🏫</span>
                    <span class="truncate">Trainer / Faculty</span>
                </button>
                <button 
                    type="button" 
                    wire:click="fillDemoCredentials('accountant')" 
                    class="px-2 py-1 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-left truncate flex items-center gap-1"
                >
                    <span>💼</span>
                    <span class="truncate">Accountant</span>
                </button>
            </div>
        </details>
    @endif
</x-filament-panels::page.simple>
