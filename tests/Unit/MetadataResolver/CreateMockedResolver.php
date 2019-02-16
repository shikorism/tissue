<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\Resolver;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\AbstractHandler;

trait CreateMockedResolver
{
    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var AbstractHandler
     */
    protected $handler;

    /**
     * @param string $resolverClass
     * @param string $responseText
     * @param array $headers
     * @param int $status
     * @return Resolver
     */
    protected function createResolver(string $resolverClass, string $responseText, array $headers = [], int $status = 200)
    {
        if (!$this->shouldUseMock()) {
            $this->resolver = app()->make($resolverClass);

            return $this->resolver;
        }

        $headers += [
            'content-type' => 'text/html',
        ];

        $mockResponse = new Response($status, $headers, $responseText);
        $this->handler = new MockHandler([$mockResponse]);
        $client = new Client(['handler' => $this->handler]);
        $this->resolver = app()->make($resolverClass, ['client' => $client]);
        return $this->resolver;
    }

    protected function shouldUseMock(): bool
    {
        return (bool)env('TEST_USE_HTTP_MOCK', true);
    }
}
