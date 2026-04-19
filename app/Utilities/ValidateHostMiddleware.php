<?php
declare(strict_types=1);

namespace App\Utilities;

use App\MetadataResolver\DeniedHostException;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * プライベート/予約済みIPレンジ等、許可されていないホストへのリクエストをブロックするGuzzle Middleware
 */
class ValidateHostMiddleware
{
    private const ALLOWED_SCHEMES = ['http', 'https'];

    /** @var callable(string): string[] */
    private $dnsResolver;

    /** @var string[] */
    private array $ownAddresses;

    /**
     * @param callable(string): string[]|null $dnsResolver ホスト名からIPアドレスの配列を返すcallable。nullの場合はdns_get_recordを使用。
     */
    public function __construct(?callable $dnsResolver = null)
    {
        $this->dnsResolver = $dnsResolver ?? self::defaultDnsResolver(...);
    }

    public function __invoke(callable $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $uri = $request->getUri();
            $scheme = strtolower($uri->getScheme());
            $host = $uri->getHost();

            // スキーム検証
            if (!in_array($scheme, self::ALLOWED_SCHEMES, true)) {
                throw new DeniedHostException((string) $uri);
            }

            // IPアドレスの正引き
            $resolvedIps = $this->resolveHost($host);
            if (empty($resolvedIps)) {
                throw new DeniedHostException((string) $uri);
            }

            // すべての解決済みIPを検証
            foreach ($resolvedIps as $ip) {
                if (IpUtils::isPrivateIp($ip)) {
                    throw new DeniedHostException((string) $uri);
                }
                if ($this->isOwnIp($ip)) {
                    throw new DeniedHostException((string) $uri);
                }
            }

            // CURLOPT_RESOLVEで解決済みIPを固定し、DNS rebindingを防止
            // IPv6アドレスは [] で囲む必要がある (https://curl.se/libcurl/c/CURLOPT_RESOLVE.html)
            $port = $uri->getPort() ?? ($scheme === 'https' ? 443 : 80);
            $formattedIps = array_map(
                fn (string $ip) => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false ? "[{$ip}]" : $ip,
                $resolvedIps
            );
            $ipsJoined = implode(',', $formattedIps);
            $options['curl'][CURLOPT_RESOLVE] = [
                ...($options['curl'][CURLOPT_RESOLVE] ?? []),
                "{$host}:{$port}:{$ipsJoined}"
            ];

            return $next($request, $options);
        };
    }

    /**
     * ホスト名またはIPリテラルからIPアドレスの配列を返す。
     * @return string[]
     */
    private function resolveHost(string $host): array
    {
        // IPv6
        $bareHost = trim($host, '[]');
        if (filter_var($bareHost, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            return [$bareHost];
        }

        // IPv4
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            return [$host];
        }

        // ホスト名 → DNS解決
        return ($this->dnsResolver)($host);
    }

    /**
     * 指定されたIPがアプリケーション自身を指すかどうかを判定する。
     * @param string $ip
     * @return bool
     */
    private function isOwnIp(string $ip): bool
    {
        if (!isset($this->ownAddresses)) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            if ($appHost === null || $appHost === false) {
                $this->ownAddresses = [];

                return false;
            }

            $this->ownAddresses = $this->resolveHost($appHost);
        }

        foreach ($this->ownAddresses as $ownAddress) {
            if (IpUtils::checkIp($ip, $ownAddress)) {
                return true;
            }
        }

        return false;
    }

    /**
     * デフォルトのDNSリゾルバ。A/AAAAレコードを両方取得する。
     * @return string[]
     */
    private static function defaultDnsResolver(string $hostname): array
    {
        $records = @dns_get_record($hostname, DNS_A | DNS_AAAA);
        if ($records === false) {
            return [];
        }

        $ips = [];
        foreach ($records as $record) {
            if (isset($record['ip'])) {
                $ips[] = $record['ip'];
            }
            if (isset($record['ipv6'])) {
                $ips[] = $record['ipv6'];
            }
        }

        return $ips;
    }
}
