<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class MGStageResolver implements Resolver
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * HTMLからタグとして利用可能な情報を抽出する
     * @param string $html ページ HTML
     * @return string[] タグ
     */
    public function extractTags(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $genreNode = $xpath->query('//div[@class="detail_data"]//th[text()="ジャンル："]/../td');
        if ($genreNode->length === 0) {
            return [];
        }

        $tagsNode = $genreNode->item(0)->getElementsByTagName('a');
        $tags = [];

        for ($i = 0; $i <= $tagsNode->length - 1; $i++) {
            $tags[] = trim($tagsNode->item($i)->textContent);
        }

        // 重複削除
        $tags = array_values(array_unique($tags));
        sort($tags);

        return $tags;
    }

    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['adc' => '1'], 'www.mgstage.com');

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);

        $metadata = new Metadata();
        $metadata->title = trim($crawler->filter('.tag')->text(''));
        $metadata->description = trim(strip_tags($crawler->filter('.txt.introduction')->text('')));
        $metadata->image = $crawler->filter('meta[property="og:image"]')->attr('content');
        $metadata->tags = $this->extractTags($html);

        return $metadata;
    }
}
