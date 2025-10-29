<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Composer para pasar user y userRole al partial del navbar
        View::composer('partials.navbar', function ($view) {
            $user = Auth::user();
            $view->with('user', $user)
                 ->with('userRole', $user ? ($user->role ? $user->role->name : null) : null);
        });
    }
}
