<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportRedirects\Redirector;

class FranchiseLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.franchise-login';

    public string $selectedRole = 'franchise_owner';

    public function mount(): void
    {
        parent::mount();

        $role = request()->query('role', 'franchise_owner');
        if (in_array($role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant'])) {
            $this->selectedRole = $role;
            $this->fillDemoCredentials($role);
        }
    }

    public function selectRole(string $role): void
    {
        $this->selectedRole = $role;
        $this->fillDemoCredentials($role);
    }

    public function fillDemoCredentials(string $role): void
    {
        $credentials = match ($role) {
            'franchise_owner' => ['email' => 'owner@apextech.com', 'password' => 'password'],
            'branch_admin' => ['email' => 'admin@apex-downtown.com', 'password' => 'password'],
            'trainer' => ['email' => 'trainer@apextech.com', 'password' => 'password'],
            'accountant' => ['email' => 'accountant@apextech.com', 'password' => 'password'],
            default => ['email' => '', 'password' => ''],
        };

        $this->form->fill([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'remember' => true,
        ]);
    }

    public function getSubheading(): string | Htmlable | null
    {
        $adminUrl = url('/admin/login');
        return new HtmlString('Institute SaaS Portal. Super Admin? <a href="' . $adminUrl . '" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">Sign in to SaaS Central &rarr;</a>');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // If super admin logs in here, direct to central admin
        if ($user && $user->isSuperAdmin()) {
            session()->regenerate();
            $target = url('/admin');
            
            return new class($target) implements LoginResponse {
                public function __construct(protected string $url) {}
                public function toResponse($request): RedirectResponse | Redirector
                {
                    return redirect()->intended($this->url);
                }
            };
        }

        // If student, route to student dashboard
        if ($user && $user->role === 'student') {
            session()->regenerate();
            $target = route('student.dashboard');
            
            return new class($target) implements LoginResponse {
                public function __construct(protected string $url) {}
                public function toResponse($request): RedirectResponse | Redirector
                {
                    return redirect()->intended($this->url);
                }
            };
        }

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        // Direct franchise staff to their specific franchise slug
        if ($user && $user->franchise) {
            $target = url("/app/{$user->franchise->slug}");
            
            return new class($target) implements LoginResponse {
                public function __construct(protected string $url) {}
                public function toResponse($request): RedirectResponse | Redirector
                {
                    return redirect()->intended($this->url);
                }
            };
        }

        return app(LoginResponse::class);
    }
}
