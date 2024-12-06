<?php
declare(strict_types=1);

namespace App\Utilities;

use App\ContentProvider;
use App\MetadataResolver\DeniedHostException;
use App\MetadataResolver\DisallowedByProviderException;
use Carbon\CarbonInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;

/**
 * リクエストに対して {@see ContentProvider} のポリシーを適用するGuzzle Middleware
 */
class ApplyProviderPolicyMiddleware
{
    /**
     * @param bool $ignoreAccessInterval ContentProviderポリシー情報に基づく連続アクセス制限を無視する
     */
    public function __construct(private bool $ignoreAccessInterval = false)
    {
    }

    public function __invoke(callable $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $url = (string) $request->getUri();
            $hostWithPort = URLUtility::getHostWithPortFromUrl($url);

            // 同一オリジンへの連続アクセス制限のために排他ロックを取る
            return GlobalLock::hostLock($hostWithPort, function (?CarbonInterface $lastAccess) use ($request, $options, $next, $url) {
                $this->checkProviderPolicy($url, $this->ignoreAccessInterval ? null : $lastAccess);

                return $next($request, $options);
            });
        };
    }

    /**
     * ContentProviderポリシー情報との照合を行い、アクセス可能かチェックします。アクセスできない場合は例外をスローします。
     * @param string $url アクセス先のURL
     * @param CarbonInterface|null $lastAccess アクセス先ホストへの最終アクセス日時 (記録がある場合)
     * @throws DeniedHostException アクセス先がTissue内のブラックリストに入っている場合にスロー
     * @throws DisallowedByProviderException アクセス先のrobots.txtによって拒否されている場合にスロー
     */
    private function checkProviderPolicy(string $url, ?CarbonInterface $lastAccess)
    {
        DB::beginTransaction();
        try {
            $hostWithPort = URLUtility::getHostWithPortFromUrl($url);
            $contentProvider = ContentProvider::sharedLock()->find($hostWithPort);
            if ($contentProvider === null) {
                $contentProvider = ContentProvider::create([
                    'host' => $hostWithPort,
                    'robots' => $this->fetchRobotsTxt($url),
                    'robots_cached_at' => now(),
                ]);
            }

            if ($contentProvider->is_blocked) {
                throw new DeniedHostException($url);
            }

            // 連続アクセス制限
            if ($lastAccess !== null) {
                $elapsedSeconds = (int) $lastAccess->diffInSeconds(now());
                if ($elapsedSeconds < $contentProvider->access_interval_sec) {
                    if ($elapsedSeconds < 0) {
                        $wait = abs($elapsedSeconds) + $contentProvider->access_interval_sec;
                    } else {
                        $wait = $contentProvider->access_interval_sec - $elapsedSeconds;
                    }
                    sleep($wait);
                }
            }

            // Fetch robots.txt
            if ($contentProvider->robots_cached_at->diffInDays(now()) >= 7) {
                $contentProvider->update([
                    'robots' => $this->fetchRobotsTxt($url),
                    'robots_cached_at' => now(),
                ]);
            }

            // Check robots.txt
            $robotsParser = new \RobotsTxtParser($contentProvider->robots);
            $robotsParser->setUserAgent('TissueBot');
            $robotsDelay = $robotsParser->getDelay();
            if ($robotsDelay !== 0 && $robotsDelay >= $contentProvider->access_interval_sec) {
                $contentProvider->access_interval_sec = (int) $robotsDelay;
                $contentProvider->save();
            }
            if ($robotsParser->isDisallowed(parse_url($url, PHP_URL_PATH))) {
                throw new DisallowedByProviderException($url);
            }

            DB::commit();
        } catch (DeniedHostException | DisallowedByProviderException $e) {
            // ContentProviderのデータ更新は行うため
            DB::commit();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 指定したメタデータURLのホストが持つrobots.txtをダウンロードします。
     * @param string $url メタデータのURL
     * @return string
     */
    private function fetchRobotsTxt(string $url): ?string
    {
        $parts = parse_url($url);
        $robotsUrl = http_build_url([
            'scheme' => $parts['scheme'],
            'host' => $parts['host'],
            'port' => $parts['port'] ?? null,
            'path' => '/robots.txt'
        ]);

        $client = app(Client::class);
        try {
            $res = $client->get($robotsUrl);
            if (stripos($res->getHeaderLine('Content-Type'), 'text/plain') !== 0) {
                Log::error('robots.txtの取得に失敗: 不適切なContent-Type (' . $res->getHeaderLine('Content-Type') . ')');

                return null;
            }

            return (string) $res->getBody();
        } catch (\Exception $e) {
            Log::error("robots.txtの取得に失敗: {$e}");

            return null;
        }
    }
}
