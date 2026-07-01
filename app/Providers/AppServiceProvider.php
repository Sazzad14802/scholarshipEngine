<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Redirect authenticated users to their role-specific dashboard
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = $request->user();
            if ($user) {
                return match ($user->role) {
                    'ADMIN'   => route('admin.dashboard'),
                    'STUDENT' => route('student.dashboard'),
                    default   => '/',
                };
            }
            return '/';
        });
    }
}
