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

class AdminLogin extends BaseLogin
{
    public function getSubheading(): string | Htmlable | null
    {
        $appUrl = url('/app/login');
        return new HtmlString('Central SaaS Platform Portal. Franchise Staff? <a href="' . $appUrl . '" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">Sign in to Franchise Hub &rarr;</a>');
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
