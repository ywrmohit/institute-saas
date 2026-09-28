<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportRedirects\Redirector;

class FranchiseLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.franchise-login';

    public string $selectedRole = 'franchise_owner';

    public function mount(): void
    {
        parent::mount();

        $role = request()->query('role');
        if ($role && in_array($role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant'])) {
            $this->selectedRole = $role;
            $this->fillDemoCredentials($role);
        }
    }

    /**
     * Backward-compatible helper for programmatic role selection (tests / dev tools).
     */
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

    public function getHeading(): string | Htmlable
    {
        return 'Institute Workspace Sign In';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString('Enter your registered email or phone number to access your branch dashboard.');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email or Phone Number')
            ->placeholder('name@institute.com or 9876543210')
            ->email(false)
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Resolve user credentials from email or phone number.
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $login = trim($data['email']);
        $cleanedPhone = preg_replace('/[^0-9]/', '', $login);

        $user = User::where(function ($query) use ($login, $cleanedPhone) {
            $query->where('email', $login)
                ->orWhere('phone', $login);

            if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
                $query->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
            }
        })->first();

        return [
            'email' => $user?->email ?? $login,
            'password' => $data['password'],
        ];
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
        $login = trim($data['email']);
        $cleanedPhone = preg_replace('/[^0-9]/', '', $login);

        // Pre-check user account status
        $user = User::where(function ($query) use ($login, $cleanedPhone) {
            $query->where('email', $login)
                ->orWhere('phone', $login);

            if (!empty($cleanedPhone) && strlen($cleanedPhone) >= 7) {
                $query->orWhere('phone', 'like', '%' . substr($cleanedPhone, -10));
            }
        })->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            if ($user->status !== 'active') {
                throw ValidationException::withMessages([
                    'data.email' => 'Your account has been deactivated. Please contact your center administrator.',
                ]);
            }

            if ($user->franchise && $user->franchise->status !== 'active' && ! $user->isSuperAdmin()) {
                throw ValidationException::withMessages([
                    'data.email' => 'Your institute workspace is currently suspended. Please contact platform support.',
                ]);
            }
        }

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // 1. Super Admin: direct to central platform admin
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

        // 2. Student: direct to student learning & exam portal
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

        // 3. Franchise Staff: direct to their specific franchise tenant slug
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
