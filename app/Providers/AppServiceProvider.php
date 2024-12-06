<?php

namespace App\Providers;

use App\MetadataResolver\FxTwitterResolver;
use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\Resolver;
use App\MetadataResolver\TwitterResolver;
use App\Services\MetadataResolveService;
use App\Utilities\ApplyProviderPolicyMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Parsedown;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
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

            $paginator = $paginator->withQueryString();

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

        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MetadataResolver::class);
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
        $this->app->when(MetadataResolver::class)->needs(Client::class)->give(function ($app) {
            $stack = HandlerStack::create();
            $stack->push($app->make(ApplyProviderPolicyMiddleware::class));

            return new Client([
                RequestOptions::HEADERS => [
                    'User-Agent' => 'TissueBot/1.0'
                ],
                'handler' => $stack,
            ]);
        });
        $this->app->when(MetadataResolveService::class)
            ->needs('$circuitBreakCount')
            ->give((int) config('metadata.circuit_break_count', 5));
        $this->app->when(ApplyProviderPolicyMiddleware::class)
            ->needs('$ignoreAccessInterval')
            ->give((bool) config('metadata.ignore_access_interval', false));
        $this->app->bind(TwitterResolver::class, function ($app) {
            return $app->make(FxTwitterResolver::class);
        });
    }
}
