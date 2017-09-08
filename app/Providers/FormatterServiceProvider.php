<?php

namespace App\Providers;

use App\Utilities\Formatter;
use Illuminate\Support\ServiceProvider;

class FormatterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Formatter::class, function ($app) {
            return new Formatter();
        });
    }
}
