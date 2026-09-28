<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FilamentAuthenticate extends BaseAuthenticate
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var \App\Models\User $user */
        $user = $guard->user();
        $panel = Filament::getCurrentPanel();

        // 1. Check if user account is deactivated
        if ($user && $user->status !== 'active') {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(redirect()->to(Filament::getLoginUrl())->withErrors([
                'data.email' => 'Your account has been deactivated. Please contact your center administrator.',
            ]));
        }

        // 2. Check if user's franchise workspace is inactive/suspended
        if ($user && $user->franchise && $user->franchise->status !== 'active' && ! $user->isSuperAdmin()) {
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(redirect()->to(Filament::getLoginUrl())->withErrors([
                'data.email' => 'Your institute workspace is currently suspended. Please contact platform support.',
            ]));
        }

        // 3. Graceful cross-panel access routing (Prevent raw 403 Forbidden screen)
        if ($user instanceof FilamentUser && ! $user->canAccessPanel($panel)) {
            // A. If Super Admin accidentally opens franchise panel without tenant
            if ($user->isSuperAdmin()) {
                abort(redirect()->to(url('/admin')));
            }

            // B. If Student accidentally opens Filament admin or franchise panel
            if ($user->isStudent()) {
                abort(redirect()->to(route('student.dashboard')));
            }

            // C. If Franchise Staff accidentally navigates to /admin
            if ($user->franchise) {
                abort(redirect()->to(url('/app/' . $user->franchise->slug)));
            }

            // D. Fallback: Log out unauthorized session and redirect to panel login
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(redirect()->to(Filament::getLoginUrl())->withErrors([
                'data.email' => 'You do not have access permission for this portal. Please sign in with an authorized account.',
            ]));
        }
    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}
