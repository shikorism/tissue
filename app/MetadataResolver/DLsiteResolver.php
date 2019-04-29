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

        //スマホページの場合はPCページに正規化
        if (strpos($url, '-touch') !== false) {
            $url = str_replace('-touch', '', $url);
        }

        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            // 抽出
            $title = $xpath->query('//title')->item(0)->textContent;
            preg_match('~\[(.+)\] \| DLsite.+$~', $title, $match);
            $maker = $match[1];

            // makerに一致するthのテキストを探す
            $makerHead = $xpath->query('//a[contains(text(), "'.$maker.'")]/ancestor::tr/th')->item(0)->textContent;

            // 余分な文を消す
            $metadata->title = trim(preg_replace('~ \[.+\] \| DLsite$~', '', $metadata->title));
            $metadata->description = trim(preg_replace('~「DLsite.+」は同人誌・同人ゲーム・同人音声のダウンロードショップ。お気に入りの作品をすぐダウンロードできてすぐ楽しめる！毎日更新しているのであなたが探している作品にきっと出会えます。国内最大級の二次元総合ダウンロードショップ「DLsite」！$~', '', $metadata->description));

            // 整形
            $metadata->description = $makerHead.': ' . $maker . PHP_EOL . $metadata->description;
            $metadata->image = str_replace('img_sam.jpg', 'img_main.jpg', $metadata->image);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
