<?php

namespace App\Services;

use App\ContentProvider;
use App\Metadata;
use App\MetadataResolver\DeniedHostException;
use App\MetadataResolver\DisallowedByProviderException;
use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\ResolverCircuitBreakException;
use App\MetadataResolver\UncaughtResolverException;
use App\Tag;
use App\Utilities\Formatter;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetadataResolveService
{
    /** @var MetadataResolver */
    private $resolver;
    /** @var Formatter */
    private $formatter;

    /**
     * メタデータの解決を中断するエラー回数。この回数以上エラーしていたら処理は行わない。
     * 0以下の場合は一切中断しない。(テスト用)
     * @var int
     */
    private $circuitBreakCount;

    public function __construct(MetadataResolver $resolver, Formatter $formatter, int $circuitBreakCount)
    {
        $this->resolver = $resolver;
        $this->formatter = $formatter;
        $this->circuitBreakCount = $circuitBreakCount;
    }

    /**
     * メタデータをキャッシュまたはリモートに問い合わせて取得します。
     * @param string $url メタデータを取得したいURL
     * @return Metadata 取得できたメタデータ
     * @throws DeniedHostException アクセス先がブラックリスト入りしているため取得できなかった場合にスロー
     * @throws UncaughtResolverException Resolver内で例外が発生して取得できなかった場合にスロー
     */
    public function execute(string $url): Metadata
    {
        // URLの正規化
        $url = $this->formatter->normalizeUrl($url);

        // 自分自身は解決しない
        if (parse_url($url, PHP_URL_HOST) === parse_url(config('app.url'), PHP_URL_HOST)) {
            throw new DeniedHostException($url);
        }

        $metadata = Metadata::find($url);

        // 無かったら取得
        // TODO: ある程度古かったら再取得とかありだと思う
        if ($metadata == null || $metadata->needRefresh()) {
            $hostWithPort = $this->getHostWithPortFromUrl($url);
            $metadata = $this->hostLock($hostWithPort, function (?CarbonInterface $lastAccess) use ($url) {
                // HostLockの解放待ちをしている間に、他のプロセスで取得完了しているかもしれない
                $metadata = Metadata::find($url);
                if ($metadata !== null && !$metadata->needRefresh()) {
                    return $metadata;
                }

                $this->checkProviderPolicy($url, $lastAccess);

                return $this->resolve($url, $metadata);
            });
        }

        return $metadata;
    }

    /**
     * URLからホスト部とポート部を抽出
     * @param string $url
     * @return string
     */
    private function getHostWithPortFromUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = $parts['host'];
        if (isset($parts['port'])) {
            $host .= ':' . $parts['port'];
        }

        return $host;
    }

    /**
     * アクセス先ホスト単位の排他ロックを取って処理を実行
     * @param string $host
     * @param callable $fn
     * @return mixed return of $fn
     * @throws \RuntimeException いろいろな死に方をする
     */
    private function hostLock(string $host, callable $fn)
    {
        $lockDir = storage_path('content_providers_lock');
        if (!file_exists($lockDir)) {
            if (!mkdir($lockDir)) {
                throw new \RuntimeException("Lock failed! Can't create lock directory.");
            }
        }

        $lockFile = $lockDir . DIRECTORY_SEPARATOR . $host;
        $fp = fopen($lockFile, 'c+b');
        if ($fp === false) {
            throw new \RuntimeException("Lock failed! Can't open lock file.");
        }

        try {
            if (!flock($fp, LOCK_EX)) {
                throw new \RuntimeException("Lock failed! Can't lock file.");
            }

            try {
                $accessInfoText = stream_get_contents($fp);
                if ($accessInfoText !== false) {
                    $accessInfo = json_decode($accessInfoText, true);
                }

                $result = $fn(isset($accessInfo['time']) ? new Carbon($accessInfo['time']) : null);

                $accessInfo = [
                    'time' => now()->toIso8601String()
                ];
                fseek($fp, 0);
                if (fwrite($fp, json_encode($accessInfo)) === false) {
                    throw new \RuntimeException("I/O Error! Can't write to lock file.");
                }

                return $result;
            } finally {
                if (!flock($fp, LOCK_UN)) {
                    throw new \RuntimeException("Unlock failed! Can't unlock file.");
                }
            }
        } finally {
            if (!fclose($fp)) {
                throw new \RuntimeException("Unlock failed! Can't close lock file.");
            }
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

    /**
     * ContentProviderポリシー情報との照合を行い、アクセス可能かチェックします。アクセスできない場合は例外をスローします。
     * @param string $url メタデータを取得したいURL
     * @param CarbonInterface|null $lastAccess アクセス先ホストへの最終アクセス日時 (記録がある場合)
     * @throws DeniedHostException アクセス先がTissue内のブラックリストに入っている場合にスロー
     * @throws DisallowedByProviderException アクセス先のrobots.txtによって拒否されている場合にスロー
     */
    private function checkProviderPolicy(string $url, ?CarbonInterface $lastAccess): void
    {
        DB::beginTransaction();
        try {
            $hostWithPort = $this->getHostWithPortFromUrl($url);
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
                $elapsedSeconds = $lastAccess->diffInSeconds(now(), false);
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
     * メタデータをリモートサーバに問い合わせて取得します。
     * @param string $url メタデータを取得したいURL
     * @param Metadata|null $metadata キャッシュ済のメタデータ (存在する場合)
     * @return Metadata 取得できたメタデータ
     * @throws UncaughtResolverException Resolver内で例外が発生して取得できなかった場合にスロー
     * @throws ResolverCircuitBreakException 規定回数以上の解決失敗により、メタデータの取得が不能となっている場合にスロー
     */
    private function resolve(string $url, ?Metadata $metadata): Metadata
    {
        DB::beginTransaction();
        try {
            if ($metadata === null) {
                $metadata = new Metadata(['url' => $url]);
            }

            if ($this->circuitBreakCount > 0 && $metadata->error_count >= $this->circuitBreakCount) {
                throw new ResolverCircuitBreakException($metadata->error_count, $url);
            }

            try {
                $resolved = $this->resolver->resolve($url);
            } catch (\Exception $e) {
                $metadata->storeException(now(), $e);
                $metadata->save();
                throw new UncaughtResolverException(implode(': ', [
                    $metadata->error_count . '回目のメタデータ取得失敗', get_class($e), $e->getMessage()
                ]), 0, $e);
            }

            $metadata->fill([
                'title' => $resolved->title,
                'description' => $resolved->description,
                'image' => $resolved->image,
                'expires_at' => $resolved->expires_at
            ]);
            $metadata->clearError();
            $metadata->save();

            $tagIds = [];
            foreach ($resolved->normalizedTags() as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $metadata->tags()->sync($tagIds);

            DB::commit();

            return $metadata;
        } catch (UncaughtResolverException $e) {
            // Metadataにエラー情報を記録するため
            DB::commit();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
