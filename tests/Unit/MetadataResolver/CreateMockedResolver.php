<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\Resolver;
use function Clue\StreamFilter\fun;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\AbstractHandler;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @var string
     */
    protected $snapshotFilename;

    protected function fetchSnapshot(string $filename): string
    {
        $this->snapshotFilename = $filename;

        return file_get_contents($filename);
    }

    /**
     * @param string $resolverClass
     * @param string $responseText
     * @param array $headers
     * @param int $status
     * @return Resolver
     */
    protected function createResolver(string $resolverClass, string $responseText, array $headers = [], int $status = 200)
    {
        if (!$this->shouldUseMock() && !$this->shouldUpdateSnapshot()) {
            $this->resolver = app()->make($resolverClass);

            return $this->resolver;
        }

        if ($this->shouldUseMock()) {
            $headers += [
                'content-type' => 'text/html',
            ];

            $mockResponse = new Response($status, $headers, $responseText);
            $this->handler = new MockHandler([$mockResponse]);
        }

        $stack = HandlerStack::create($this->handler);
        $client = new Client(['handler' => $stack]);
        if ($this->shouldUpdateSnapshot()) {
            $stack->push($this->makeUpdateSnapshotMiddleware());
        }

        $this->resolver = app()->make($resolverClass, ['client' => $client]);

        return $this->resolver;
    }

    protected function shouldUseMock(): bool
    {
        return (bool) env('TEST_USE_HTTP_MOCK', true);
    }

    protected function shouldUpdateSnapshot(): bool
    {
        return (bool) env('TEST_UPDATE_SNAPSHOT', false);
    }

    protected function makeUpdateSnapshotMiddleware(): callable
    {
        return function (callable $next) {
            return function (RequestInterface $request, array $options) use ($next) {
                return $next($request, $options)->then(function (ResponseInterface $response) {
                    if (empty($this->snapshotFilename)) {
                        throw new \RuntimeException('スナップショットのファイル名が分かりません。file_get_contents()を使っている場合、fetchSnapshot()に置き換えてください。');
                    }

                    file_put_contents($this->snapshotFilename, (string) $response->getBody());
                    fwrite(STDERR, "Snapshot Updated: {$this->snapshotFilename}\n");

                    return $response;
                });
            };
        };
    }
}
