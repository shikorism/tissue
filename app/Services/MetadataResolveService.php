<?php

namespace App\Services;

use App\Metadata;
use App\MetadataResolver\DeniedHostException;
use App\MetadataResolver\MetadataResolver;
use App\MetadataResolver\ResolverCircuitBreakException;
use App\MetadataResolver\UncaughtResolverException;
use App\Tag;
use App\Utilities\Formatter;
use Illuminate\Support\Facades\DB;

class MetadataResolveService
{
    /** @var int メタデータの解決を中断するエラー回数。この回数以上エラーしていたら処理は行わない。 */
    const CIRCUIT_BREAK_COUNT = 5;

    /** @var MetadataResolver */
    private $resolver;
    /** @var Formatter */
    private $formatter;

    public function __construct(MetadataResolver $resolver, Formatter $formatter)
    {
        $this->resolver = $resolver;
        $this->formatter = $formatter;
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

        DB::beginTransaction();
        try {
            $metadata = Metadata::find($url);

            // 無かったら取得
            // TODO: ある程度古かったら再取得とかありだと思う
            if ($metadata == null || $metadata->needRefresh()) {
                if ($metadata === null) {
                    $metadata = new Metadata(['url' => $url]);
                }

                if ($metadata->error_count >= self::CIRCUIT_BREAK_COUNT) {
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
            }

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
