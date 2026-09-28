<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            $status = $e->getStatusCode();

            // 1. Handle 403 Forbidden: smart auto-routing for authenticated users
            if ($status === 403) {
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if ($user->isSuperAdmin() && ! $request->is('admin*')) {
                        return redirect('/admin');
                    }
                    if ($user->isStudent() && ! $request->is('student*')) {
                        return redirect()->route('student.dashboard');
                    }
                    if ($user->franchise && ! $request->is('app/' . $user->franchise->slug . '*')) {
                        return redirect('/app/' . $user->franchise->slug);
                    }
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Forbidden: You do not have permission to access this area.',
                    ], 403);
                }

                return response()->view('errors.403', [
                    'message' => $e->getMessage() ?: 'You do not have permission to access this workspace with your current account.',
                ], 403);
            }

            // 2. Handle 419 Page Expired (CSRF): redirect back with clean message
            if ($status === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your session has expired. Please refresh and try again.'], 419);
                }
                return back()->withErrors(['data.email' => 'Your session has expired. Please sign in again.'])->withInput();
            }
        });
    })->create();
