<?php

namespace App\Providers;

use App\Ejaculation;
use App\Policies\EjaculationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Ejaculation::class => EjaculationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Passport::hashClientSecrets();
        Passport::personalAccessTokensExpireIn(now()->addYears(10));

        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });
    }
}
