<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-patient-history', function (User $user, User $paciente) {
            return $user->rol === 'admin'
                || ($user->rol === 'paciente' && $user->id === $paciente->id);
        });
    }
}
