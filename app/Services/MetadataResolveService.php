<?php

namespace App\Services;

use App\Metadata;
use App\MetadataResolver\MetadataResolver;
use App\Tag;
use App\Utilities\Formatter;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Log;

class MetadataResolveService
{
    /** @var MetadataResolver */
    private $resolver;
    /** @var Formatter */
    private $formatter;

    public function __construct(MetadataResolver $resolver, Formatter $formatter)
    {
        $this->resolver = $resolver;
        $this->formatter = $formatter;
    }

    public function execute(string $url): Metadata
    {
        // URLの正規化
        $url = $this->formatter->normalizeUrl($url);

        // 無かったら取得
        // TODO: ある程度古かったら再取得とかありだと思う
        $metadata = Metadata::find($url);
        if ($metadata == null || ($metadata->expires_at !== null && $metadata->expires_at < now())) {
            try {
                $resolved = $this->resolver->resolve($url);
                $metadata = Metadata::updateOrCreate(['url' => $url], [
                    'title' => $resolved->title,
                    'description' => $resolved->description,
                    'image' => $resolved->image,
                    'expires_at' => $resolved->expires_at
                ]);

                $tagIds = [];
                foreach ($resolved->normalizedTags() as $tagName) {
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $metadata->tags()->sync($tagIds);
            } catch (TransferException $e) {
                // 何らかの通信エラーによってメタデータの取得に失敗した時、とりあえずエラーログにURLを残す
                Log::error(self::class . ': メタデータの取得に失敗 URL=' . $url);
                throw $e;
            }
        }

        return $metadata;
    }
}
