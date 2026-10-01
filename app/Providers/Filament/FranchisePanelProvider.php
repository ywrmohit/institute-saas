<?php

namespace App\Providers\Filament;

use App\Models\Franchise;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FranchisePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('franchise')
            ->path('app')
            ->login(\App\Filament\Pages\Auth\FranchiseLogin::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)

            // ─── Brand Identity (Option 1: Modern SaaS Standard) ───────────
            ->brandName(function (): string {
                $tenant = \Filament\Facades\Filament::getTenant();
                return $tenant ? $tenant->name : (\App\Models\SystemSetting::get('app_name', null, 'Remax') . ' Institute Hub');
            })
            ->brandLogo(function () {
                $tenant = \Filament\Facades\Filament::getTenant();
                $name = $tenant ? $tenant->name : (\App\Models\SystemSetting::get('app_name', null, 'Remax') . ' Institute Hub');
                $logoUrl = ($tenant && $tenant->logo) ? asset('storage/' . $tenant->logo) : null;

                if ($logoUrl) {
                    return new \Illuminate\Support\HtmlString('
                        <div style="display:flex;align-items:center;gap:0.625rem;max-width:145px;min-width:0;">
                            <img src="' . e($logoUrl) . '" alt="' . e($name) . '" style="height:32px;width:32px;border-radius:6px;object-fit:cover;flex-shrink:0;box-shadow:0 1px 2px rgba(0,0,0,0.12);" />
                            <span style="font-size:0.875rem;font-weight:600;letter-spacing:-0.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.2;" class="text-gray-950 dark:text-white" title="' . e($name) . '">' . e($name) . '</span>
                        </div>
                    ');
                }

                return new \Illuminate\Support\HtmlString('
                    <div style="display:flex;align-items:center;gap:0.5rem;max-width:145px;min-width:0;">
                        <div style="display:flex;height:32px;width:32px;align-items:center;justify-content:center;border-radius:6px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;font-weight:700;font-size:0.8rem;box-shadow:0 1px 2px rgba(0,0,0,0.15);flex-shrink:0;">
                            ' . e(strtoupper(substr($name, 0, 2))) . '
                        </div>
                        <span style="font-size:0.875rem;font-weight:600;letter-spacing:-0.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.2;" class="text-gray-950 dark:text-white" title="' . e($name) . '">' . e($name) . '</span>
                    </div>
                ');
            })
            ->brandLogoHeight('auto')
            ->favicon(function (): ?string {
                $tenant = \Filament\Facades\Filament::getTenant();
                if ($tenant && $tenant->logo) {
                    return asset('storage/' . $tenant->logo);
                }
                return null;
            })

            // ─── Navigation & UX ───────────────────────────────────────────
            ->sidebarCollapsibleOnDesktop()          // ← collapse sidebar to icons on desktop
            ->databaseNotifications()               // ← bell icon with unread badge

            // ─── User Menu (top-right avatar dropdown) ─────────────────────
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('My Personal Profile')
                    ->url(function (): string {
                        $tenant = \Filament\Facades\Filament::getTenant() ?? auth()->user()?->franchise;
                        return $tenant ? \App\Filament\Franchise\Pages\UserProfile::getUrl(['tenant' => $tenant]) : url('/app/profile');
                    })
                    ->icon('heroicon-o-user-circle'),

                'institute_profile' => MenuItem::make()
                    ->label('Institute Profile & Branding')
                    ->url(function (): string {
                        $tenant = \Filament\Facades\Filament::getTenant() ?? auth()->user()?->franchise;
                        return $tenant ? \App\Filament\Franchise\Pages\InstituteProfile::getUrl(['tenant' => $tenant]) : '#';
                    })
                    ->visible(fn (): bool => auth()->check() && (
                        auth()->user()->isSuperAdmin() ||
                        auth()->user()->isFranchiseOwner() ||
                        auth()->user()->isBranchAdmin() ||
                        auth()->user()->franchise_id !== null
                    ))
                    ->icon('heroicon-o-building-office-2'),
            ])

            // ─── Navigation Group Order (explicit left-to-right / top-to-bottom) ──
            ->navigationGroups([
                NavigationGroup::make('Student Lifecycle')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make('Academics')
                    ->icon('heroicon-o-book-open'),
                NavigationGroup::make('Examinations & Certs')
                    ->icon('heroicon-o-pencil-square'),
                NavigationGroup::make('Financials')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make('Center Management')
                    ->icon('heroicon-o-building-storefront')
                    ->collapsed(),
                NavigationGroup::make('Settings & Administration')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])

            // ─── Multi-tenancy ────────────────────────────────────────────
            ->tenant(Franchise::class, slugAttribute: 'slug')
            ->tenantMenu(false)             // staff belong to one institute — no switcher needed

            // ─── Theme ───────────────────────────────────────────────────
            ->colors([
                'primary' => Color::Blue,
                'gray'    => Color::Slate,
            ])
            ->darkMode(true)

            // ─── Discovery ───────────────────────────────────────────────
            ->discoverResources(in: app_path('Filament/Franchise/Resources'), for: 'App\\Filament\\Franchise\\Resources')
            ->discoverPages(in: app_path('Filament/Franchise/Pages'), for: 'App\\Filament\\Franchise\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Franchise/Widgets'), for: 'App\\Filament\\Franchise\\Widgets')
            ->widgets([
                // Widgets are automatically discovered from app/Filament/Franchise/Widgets
            ])

            // ─── Middleware ───────────────────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \App\Http\Middleware\FilamentAuthenticate::class,
            ]);
    }
}
