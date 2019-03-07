<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class DLsiteResolver implements Resolver
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
        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            // 抽出
            preg_match('~\[(.+)\] \| DLsite$~', $metadata->title, $match);
            $maker = $match[1];

            // 余分な文を消す
            $metadata->title = preg_replace('~\[.+\] \| DLsite$~', '', $metadata->title);
            $metadata->description = preg_replace('~「DLsite.+」は同人誌・同人ゲーム・同人音声のダウンロードショップ。お気に入りの作品をすぐダウンロードできてすぐ楽しめる！毎日更新しているのであなたが探している作品にきっと出会えます。国内最大級の二次元総合ダウンロードショップ「DLsite」！$~', '', $metadata->description);

            // 整形
            $metadata->description = 'サークル: ' . $maker . PHP_EOL . $metadata->description;
            $metadata->image = str_replace('img_sam.jpg', 'img_main.jpg', $metadata->image);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
