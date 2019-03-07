<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FantiaResolver implements Resolver
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var OGPResolver
     */
    private $ogpResolver;

    public function __construct(Client $client, OGPResolver $ogpResolver)
    {
        $this->client = $client;
        $this->ogpResolver = $ogpResolver;
    }

    public function resolve(string $url): Metadata
    {
        preg_match("~\d+~", $url, $match);
        $postId = $match[0];

        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $node = $xpath->query("//meta[@property='twitter:image']")->item(0);
            $ogpUrl = $node->getAttribute('content');

            // 投稿に画像がない場合（ogp.jpgでない場合）のみ大きい画像に変換する
            if ($ogpUrl != 'http://fantia.jp/images/ogp.jpg') {
                preg_match("~https://fantia\.s3\.amazonaws\.com/uploads/post/file/{$postId}/ogp_(.*?)\.(jpg|png)~", $ogpUrl, $match);
                $uuid = $match[1];
                $extension = $match[2];

                // 大きい画像に変換
                $metadata->image = "https://c.fantia.jp/uploads/post/file/{$postId}/main_{$uuid}.{$extension}";
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
