<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class NijieResolver implements Resolver
{
    /**
     * @var Client
     */
    protected $client;
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
        if (mb_strpos($url, '//sp.nijie.info') !== false) {
            $url = preg_replace('~//sp\.nijie\.info~', '//nijie.info', $url);
        }
        if (mb_strpos($url, 'view_popup.php') !== false) {
            $url = preg_replace('~view_popup\.php~', 'view.php', $url);
        }

        $res =  $this->client->get($url);
        $html = (string) $res->getBody();
        $metadata = $this->ogpResolver->parse($html);
        $crawler = new Crawler($html);

        // DomCrawler内でjson内の日本語がHTMLエンティティに変換されるのでhtml_entity_decode
        $json = html_entity_decode($crawler->filter('script[type="application/ld+json"]')->first()->text());

        // 改行がそのまま入っていることがあるのでデコード前にエスケープが必要
        $data = json_decode(preg_replace('/\r?\n/', '\n', $json), true);

        $metadata->title = $data['name'];
        $metadata->description = '投稿者: ' . $data['author']['name'] . PHP_EOL . $data['description'];
        if (
            isset($data['thumbnailUrl']) &&
            !ends_with($data['thumbnailUrl'], '.gif') &&
            !ends_with($data['thumbnailUrl'], '.mp4')
        ) {
            // サムネイルからメイン画像に
            $metadata->image = str_replace('__rs_l160x160/', '', $data['thumbnailUrl']);
        }
        $metadata->tags = $crawler->filter('#view-tag span.tag_name')->extract('_text');

        return $metadata;
    }
}
