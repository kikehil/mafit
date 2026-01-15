<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 
    ];

    public function boot(): void
    {
        // Gate para admin
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        // Gate para importar MAF
        Gate::define('import-maf', function ($user) {
            return $user->isAdmin();
        });

        // Gate para ver batches
        Gate::define('view-maf-batches', function ($user) {
            return $user->isAdmin();
        });
    }
}









