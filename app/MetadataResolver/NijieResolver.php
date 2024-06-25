<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
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

        $json = $crawler->filter('script[type="application/ld+json"]')->first()->text(null, false);

        // 改行がそのまま入っていることがあるのでデコード前にエスケープが必要
        $data = json_decode(preg_replace('/\r?\n/', '\n', $json), true);

        // DomCrawler内でjson内の日本語がHTMLエンティティに変換されるので、全要素に対してhtml_entity_decode
        array_walk_recursive($data, function (&$v) {
            $v = html_entity_decode($v);
        });

        $metadata->title = $data['name'];
        $metadata->description = '投稿者: ' . $data['author']['name'] . PHP_EOL . $data['description'];
        if (
            isset($data['thumbnailUrl']) &&
            !Str::endsWith($data['thumbnailUrl'], '.gif') &&
            !Str::endsWith($data['thumbnailUrl'], '.mp4')
        ) {
            // サムネイルからメイン画像に
            $metadata->image = str_replace('__s_rs_l160x160/', '__s_rs_l0x0/', $data['thumbnailUrl']);
        }
        $metadata->tags = $crawler->filter('#view-tag span.tag_name')->extract(['_text']);

        return $metadata;
    }
}
