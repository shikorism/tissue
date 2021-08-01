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
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Ejaculation::class => EjaculationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }
        Passport::hashClientSecrets();
        Passport::personalAccessClientId(config('passport.personal_access_client.id'));
        Passport::personalAccessClientSecret(config('passport.personal_access_client.secret'));

        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });
    }
}
