<?php

namespace App\Providers;

use App\MetadataResolver\MetadataResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MetadataResolver::class, function ($app) {
            return new MetadataResolver();
        });
    }
}
