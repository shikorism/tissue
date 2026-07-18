<?php
declare(strict_types=1);

namespace Tests\Unit\Utilities;

use App\MetadataResolver\DeniedHostException;
use App\Utilities\ValidateHostMiddleware;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class ValidateHostMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => '']);
    }

    private function createMiddleware(callable $dnsResolver): ValidateHostMiddleware
    {
        return new ValidateHostMiddleware($dnsResolver);
    }

    private function sendRequest(ValidateHostMiddleware $middleware, string $url): array
    {
        $capturedOptions = null;
        $mock = new MockHandler([
            function (Request $request, array $options) use (&$capturedOptions) {
                $capturedOptions = $options;

                return new Response(200);
            },
        ]);

        $stack = HandlerStack::create($mock);
        $stack->push($middleware);

        $handler = $stack->resolve();
        $handler(new Request('GET', $url), []);

        return $capturedOptions ?? [];
    }

    /**
     * @dataProvider providePrivateIpv4Addresses
     */
    public function testBlocksPrivateIpv4(string $ip): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(fn () => [$ip]);
        $this->sendRequest($middleware, 'https://example.com/');
    }

    public static function providePrivateIpv4Addresses(): array
    {
        return [
            'loopback' => ['127.0.0.1'],
            'loopback high' => ['127.255.255.255'],
            'class A private' => ['10.0.0.1'],
            'class B private' => ['172.16.0.1'],
            'class C private' => ['192.168.1.1'],
            'link-local' => ['169.254.1.1'],
            'zero' => ['0.0.0.0'],
        ];
    }

    /**
     * @dataProvider providePrivateIpv6Addresses
     */
    public function testBlocksPrivateIpv6(string $ip): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(fn () => [$ip]);
        $this->sendRequest($middleware, 'https://example.com/');
    }

    public static function providePrivateIpv6Addresses(): array
    {
        return [
            'loopback' => ['::1'],
            'unique local' => ['fc00::1'],
            'link-local' => ['fe80::1'],
            'IPv4-mapped loopback' => ['::ffff:127.0.0.1'],
            'IPv4-mapped class A private' => ['::ffff:10.0.0.1'],
            'IPv4-mapped class B private' => ['::ffff:172.16.0.1'],
            'IPv4-mapped class C private' => ['::ffff:192.168.1.1'],
            'IPv4-mapped link-local' => ['::ffff:169.254.1.1'],
        ];
    }

    public function testAllowsPublicIp(): void
    {
        $middleware = $this->createMiddleware(fn () => ['93.184.216.34']);
        $options = $this->sendRequest($middleware, 'https://example.com/path');

        $this->assertArrayHasKey(CURLOPT_RESOLVE, $options['curl']);
        $this->assertContains('example.com:443:93.184.216.34', $options['curl'][CURLOPT_RESOLVE]);
    }

    public function testBlocksDualStackWithPrivateAddress(): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(fn () => ['93.184.216.34', '::1']);
        $this->sendRequest($middleware, 'https://example.com/');
    }

    public function testBlocksDnsResolutionFailure(): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(fn () => []);
        $this->sendRequest($middleware, 'https://example.com/');
    }

    /**
     * @dataProvider provideBlockedSchemes
     */
    public function testBlocksNonHttpSchemes(string $url): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(fn () => ['93.184.216.34']);
        $this->sendRequest($middleware, $url);
    }

    public static function provideBlockedSchemes(): array
    {
        return [
            'file' => ['file:///etc/passwd'],
            'gopher' => ['gopher://evil.com/'],
            'ftp' => ['ftp://evil.com/'],
            'data' => ['data://text/plain;base64,SGVsbG8='],
        ];
    }

    public function testBlocksIpv4Literal(): void
    {
        $this->expectException(DeniedHostException::class);

        // DNSリゾルバは呼ばれないはず
        $middleware = $this->createMiddleware(function () {
            $this->fail('DNS resolver should not be called for IP literals');
        });
        $this->sendRequest($middleware, 'http://127.0.0.1/admin');
    }

    public function testBlocksIpv6Literal(): void
    {
        $this->expectException(DeniedHostException::class);

        $middleware = $this->createMiddleware(function () {
            $this->fail('DNS resolver should not be called for IP literals');
        });
        $this->sendRequest($middleware, 'http://[::1]/admin');
    }

    public function testAllowsPublicIpv4Literal(): void
    {
        $middleware = $this->createMiddleware(function () {
            $this->fail('DNS resolver should not be called for IP literals');
        });
        $options = $this->sendRequest($middleware, 'http://93.184.216.34/path');

        $this->assertArrayHasKey(CURLOPT_RESOLVE, $options['curl']);
    }

    public function testBlocksOwnIp(): void
    {
        $this->expectException(DeniedHostException::class);

        config(['app.url' => 'https://tissue.example.com']);

        // tissue.example.comとevil.comが同じIPに解決される
        $middleware = $this->createMiddleware(fn () => ['203.0.113.50']);
        $this->sendRequest($middleware, 'https://evil.com/');
    }

    public function testBlocksOwnIpv6(): void
    {
        $this->expectException(DeniedHostException::class);

        config(['app.url' => 'https://tissue.example.com']);

        $middleware = $this->createMiddleware(fn (string $host) => match ($host) {
            'tissue.example.com' => ['2001:db8::1'],
            default => ['2001:db8::1'],
        });
        $this->sendRequest($middleware, 'https://evil.com/');
    }

    public function testAllowsDifferentIpFromOwn(): void
    {
        config(['app.url' => 'https://tissue.example.com']);

        $middleware = $this->createMiddleware(fn (string $host) => match ($host) {
            'tissue.example.com' => ['203.0.113.50'],
            default => ['198.51.100.10'],
        });
        $options = $this->sendRequest($middleware, 'https://other.com/');

        $this->assertArrayHasKey(CURLOPT_RESOLVE, $options['curl']);
    }

    public function testOwnAddressCheckSkippedWhenAppUrlNotConfigured(): void
    {
        config(['app.url' => '']);

        $middleware = $this->createMiddleware(fn () => ['198.51.100.10']);
        $options = $this->sendRequest($middleware, 'https://example.com/');

        $this->assertArrayHasKey(CURLOPT_RESOLVE, $options['curl']);
    }

    public function testSetsCurloptResolveWithCorrectPort(): void
    {
        $middleware = $this->createMiddleware(fn () => ['93.184.216.34', '2606:2800:220:1:248:1893:25c8:1946']);

        // HTTP → port 80, カンマ区切りで1エントリ、IPv6は[]で囲む
        $httpOptions = $this->sendRequest($middleware, 'http://example.com/');
        $this->assertContains('example.com:80:93.184.216.34,[2606:2800:220:1:248:1893:25c8:1946]', $httpOptions['curl'][CURLOPT_RESOLVE]);

        // HTTPS → port 443
        $httpsOptions = $this->sendRequest($middleware, 'https://example.com/');
        $this->assertContains('example.com:443:93.184.216.34,[2606:2800:220:1:248:1893:25c8:1946]', $httpsOptions['curl'][CURLOPT_RESOLVE]);

        // Custom port
        $customOptions = $this->sendRequest($middleware, 'https://example.com:8443/');
        $this->assertContains('example.com:8443:93.184.216.34,[2606:2800:220:1:248:1893:25c8:1946]', $customOptions['curl'][CURLOPT_RESOLVE]);
    }
}
