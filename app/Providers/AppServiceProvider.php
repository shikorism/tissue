<?php

namespace App\Providers;

use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\TwitterApiResolver;
use App\MetadataResolver\TwitterOGPResolver;
use App\MetadataResolver\TwitterResolver;
use App\Services\MetadataResolveService;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
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

        JsonResource::withoutWrapping();

        Response::macro('fromPaginator', function ($paginator, $resourceClass) {
            if (!($paginator instanceof LengthAwarePaginator)) {
                throw new \LogicException('invalid type');
            }

            $headers = [];

            $links = [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ];
            foreach ($links as $rel => $link) {
                unset($links[$rel]);
                if (!empty($link)) {
                    $links[] = sprintf('<%s>; rel="%s"', $link, $rel);
                }
            }
            if (!empty($links)) {
                $headers['Link'] = implode(',', $links);
            }

            $headers['X-Total-Count'] = $paginator->total();

            return Response::json(
                $paginator->getCollection()->map(function ($item) use ($resourceClass) {
                    return new $resourceClass($item);
                }),
                200,
                $headers
            );
        });
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
        $this->app->bind(Client::class, function () {
            return new Client([
                RequestOptions::HEADERS => [
                    'User-Agent' => 'TissueBot/1.0'
                ]
            ]);
        });
        $this->app->when(MetadataResolveService::class)
            ->needs('$circuitBreakCount')
            ->give((int) config('metadata.circuit_break_count', 5));
        $this->app->when(MetadataResolveService::class)
            ->needs('$ignoreAccessInterval')
            ->give((bool) config('metadata.ignore_access_interval', false));
        $this->app->bind(TwitterResolver::class, function ($app) {
            if (empty(config('twitter.bearer_token'))) {
                return $app->make(TwitterOGPResolver::class);
            } else {
                return $app->make(TwitterApiResolver::class);
            }
        });
    }
}
