<?php

namespace Tests\Unit\Services;

use App\ContentProvider;
use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\ResolverCircuitBreakException;
use App\MetadataResolver\UncaughtResolverException;
use App\Services\MetadataResolveService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class MetadataResolverServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Carbon::setTestNow('2020-07-21 19:19:19');
        // FIXME: 今書かれてるテストはresolveのHTTPリクエストのみを考慮しているので、ContentProviderにデータがないとリクエスト回数がずれる
        ContentProvider::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testOnRuntimeException()
    {
        $this->mock(MetadataResolver::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturnUsing(function ($url) {
                throw new \RuntimeException('Something happened!');
            });
        });

        try {
            $service = app()->make(MetadataResolveService::class);
            $service->execute('http://example.com');
        } catch (UncaughtResolverException $e) {
            $this->assertDatabaseHas('metadata', [
                'url' => 'http://example.com',
                'error_at' => new Carbon('2020-07-21 19:19:19'),
                'error_count' => 1,
                'error_exception_class' => \RuntimeException::class,
                'error_http_code' => null,
                'error_body' => 'Something happened!',
            ]);

            return;
        }
        $this->fail();
    }

    public function testOnHttpClientError()
    {
        $handler = HandlerStack::create(new MockHandler([new Response(404)]));
        $client = new Client(['handler' => $handler]);
        $this->instance(Client::class, $client);
        $this->app->when(MetadataResolver::class)->needs(Client::class)->give(fn () => $client);

        try {
            $service = app()->make(MetadataResolveService::class);
            $service->execute('http://example.com');
        } catch (UncaughtResolverException $e) {
            $this->assertDatabaseHas('metadata', [
                'url' => 'http://example.com',
                'error_at' => new Carbon('2020-07-21 19:19:19'),
                'error_count' => 1,
                'error_exception_class' => ClientException::class,
                'error_http_code' => 404,
            ]);

            return;
        }
        $this->fail();
    }

    public function testOnHttpServerError()
    {
        $handler = HandlerStack::create(new MockHandler([new Response(503), new Response(503)]));
        $client = new Client(['handler' => $handler]);
        $this->instance(Client::class, $client);
        $this->app->when(MetadataResolver::class)->needs(Client::class)->give(fn () => $client);

        try {
            $service = app()->make(MetadataResolveService::class);
            $service->execute('http://example.com');
        } catch (UncaughtResolverException $e) {
            $this->assertDatabaseHas('metadata', [
                'url' => 'http://example.com',
                'error_at' => new Carbon('2020-07-21 19:19:19'),
                'error_count' => 1,
                'error_exception_class' => ServerException::class,
                'error_http_code' => 503,
            ]);

            return;
        }
        $this->fail();
    }

    public function testCircuitBreak()
    {
        $this->mock(MetadataResolver::class, function (MockInterface $mock) {
            $mock->shouldReceive('resolve')->andReturnUsing(function ($url) {
                throw new \RuntimeException('Something happened!');
            });
        });

        try {
            for ($i = 0; $i < 6; $i++) {
                try {
                    $service = app()->make(MetadataResolveService::class);
                    $service->execute('http://example.com');
                } catch (UncaughtResolverException $e) {
                }
            }
        } catch (ResolverCircuitBreakException $e) {
            $this->assertDatabaseHas('metadata', [
                'url' => 'http://example.com',
                'error_at' => new Carbon('2020-07-21 19:19:19'),
                'error_count' => 5,
                'error_exception_class' => \RuntimeException::class,
                'error_http_code' => null,
                'error_body' => 'Something happened!',
            ]);

            return;
        }
        $this->fail();
    }

    public function testOnResurrect()
    {
        $successBody = <<<HTML
<!doctype html>
<html lang="ja">
<head>
<meta charset="UTF-8">
    <meta name="og:title" content="OGP Title">
    <meta name="og:description" content="OGP Description">
    <title>Test Document</title>
</head>
<body>
</body>
</html>
HTML;
        $handler = HandlerStack::create(new MockHandler([
            new Response(404),
            new Response(200, ['Content-Type' => 'text/html'], $successBody),
        ]));
        $client = new Client(['handler' => $handler]);
        $this->instance(Client::class, $client);
        $this->app->when(MetadataResolver::class)->needs(Client::class)->give(fn () => $client);

        for ($i = 0; $i < 2; $i++) {
            try {
                $service = app()->make(MetadataResolveService::class);
                $service->execute('http://example.com');
            } catch (UncaughtResolverException $e) {
            }
        }

        $this->assertDatabaseHas('metadata', [
            'url' => 'http://example.com',
            'title' => 'OGP Title',
            'description' => 'OGP Description',
            'image' => '',
            'error_at' => null,
            'error_count' => 0,
            'error_exception_class' => null,
            'error_http_code' => null,
            'error_body' => null,
        ]);
    }
}
