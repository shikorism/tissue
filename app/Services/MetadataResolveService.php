<?php

namespace App\Services;

use App\Metadata;
use App\MetadataResolver\DeniedHostException;
use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\ResolverCircuitBreakException;
use App\MetadataResolver\UncaughtResolverException;
use App\Tag;
use App\Utilities\Formatter;
use App\Utilities\GlobalLock;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

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
            $metadata = GlobalLock::urlLock($url, function (?CarbonInterface $lastAccess) use ($url) {
                // GlobalLockの解放待ちをしている間に、他のプロセスで取得完了しているかもしれない
                $metadata = Metadata::find($url);
                if ($metadata !== null && !$metadata->needRefresh()) {
                    return $metadata;
                }

                return $this->resolve($url, $metadata);
            });
        }

        return $metadata;
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
