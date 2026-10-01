<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-m-bolt"
        icon-color="primary"
    >
        <x-slot name="heading">
            <span class="text-sm font-semibold tracking-tight">Quick Operations Launcher</span>
        </x-slot>

        <x-slot name="description">
            <span class="text-xs text-gray-500 dark:text-gray-400">Frequent daily front-desk workflows</span>
        </x-slot>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    class="group flex items-center gap-3.5 rounded-lg border border-gray-200/70 bg-gray-50/60 p-3 transition duration-150 ease-in-out hover:border-primary-500 hover:bg-white hover:shadow-sm dark:border-white/10 dark:bg-white/[0.02] dark:hover:border-primary-500/50 dark:hover:bg-white/[0.05]"
                >
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $action['color'] }} shadow-sm transition-transform duration-150 group-hover:scale-105">
                        <x-filament::icon :icon="$action['icon']" class="h-5 w-5 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="truncate text-xs font-semibold text-gray-900 transition-colors group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400">
                                {{ $action['label'] }}
                            </span>
                            <x-filament::icon icon="heroicon-m-arrow-up-right" class="h-3 w-3 text-gray-400 opacity-0 transition-opacity duration-150 group-hover:opacity-100" />
                        </div>
                        <p class="truncate text-[11px] text-gray-500 dark:text-gray-400">
                            {{ $action['description'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
