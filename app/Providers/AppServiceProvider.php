<?php

namespace App\Providers;

use App\MetadataResolver\MetadataResolver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Parsedown;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('parsedown', function ($expression) {
            return "<?php echo app('parsedown')->text($expression); ?>";
        });

        stream_filter_register('convert.mbstring.*', 'Stream_Filter_Mbstring');
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
        $this->app->singleton('parsedown', function () {
            return Parsedown::instance();
        });
    }
}
