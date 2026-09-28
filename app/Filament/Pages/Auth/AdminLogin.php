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

class AdminLogin extends BaseLogin
{
    public function getHeading(): string | Htmlable
    {
        return 'SaaS Platform Administration';
    }

    public function getSubheading(): string | Htmlable | null
    {
        $appUrl = url('/app/login');
        return new HtmlString('Central Super Admin Portal. Franchise Staff? <a href="' . $appUrl . '" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">Sign in to Institute Hub &rarr;</a>');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email or Phone Number')
            ->placeholder('admin@remax.io')
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
                    'data.email' => 'Your account has been deactivated. Please contact the platform administrator.',
                ]);
            }
        }

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // If user is franchise staff attempting to login on central admin, smoothly redirect them to their Franchise portal
        if ($user && in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant'])) {
            session()->regenerate();
            $slug = $user->franchise?->slug ?? 'apex-institute';
            $target = url("/app/{$slug}");
            
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

        return app(LoginResponse::class);
    }
}
