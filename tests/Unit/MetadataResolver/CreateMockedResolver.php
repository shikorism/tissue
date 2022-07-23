<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\Resolver;
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
     * @var string[]
     */
    protected $snapshotFilenames = [];

    protected function fetchSnapshot(string $filename, int $sequence = 0): string
    {
        $this->snapshotFilenames[$sequence] = $filename;

        if (file_exists($filename)) {
            return file_get_contents($filename);
        } else {
            return '';
        }
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
        return $this->createResolverEx($resolverClass, [compact('responseText', 'headers', 'status')]);
    }

    /**
     * @param string $resolverClass
     * @param array $responses
     * @return Resolver
     */
    protected function createResolverEx(string $resolverClass, array $responses): Resolver
    {
        if (empty($responses)) {
            throw new \LogicException('$responses には1つ以上の要素が必要です。');
        }
        if (!$this->shouldUseMock() && !$this->shouldUpdateSnapshot()) {
            $this->resolver = app()->make($resolverClass);

            return $this->resolver;
        }

        if ($this->shouldUseMock()) {
            $default = [
                'headers' => [],
                'status' => 200,
            ];
            $queue = [];
            foreach ($responses as $index => $response) {
                $response = array_merge($default, $response);
                $response['headers'] += ['content-type' => 'text/html'];

                if (!isset($response['responseText'])) {
                    throw new \LogicException("\$responses[$index]['responseText'] が設定されていません。");
                }

                $queue[] = new Response($response['status'], $response['headers'], $response['responseText']);
            }
            $this->handler = new MockHandler($queue);
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
        $sequence = 0;

        return function (callable $next) use (&$sequence) {
            return function (RequestInterface $request, array $options) use ($next, &$sequence) {
                return $next($request, $options)->then(function (ResponseInterface $response) use (&$sequence) {
                    if (empty($this->snapshotFilenames[$sequence])) {
                        throw new \RuntimeException('スナップショットのファイル名が分かりません。file_get_contents()を使っている場合、fetchSnapshot()に置き換えてください。');
                    }

                    file_put_contents($this->snapshotFilenames[$sequence], (string) $response->getBody());
                    fwrite(STDERR, "Snapshot Updated: {$this->snapshotFilenames[$sequence]}\n");

                    $sequence++;

                    return $response;
                });
            };
        };
    }
}
