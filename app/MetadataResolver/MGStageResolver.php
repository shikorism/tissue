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

    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['adc' => '1'], 'www.mgstage.com');

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);

        $metadata = new Metadata();
        $metadata->title = trim($crawler->filter('.tag')->text(''));
        $metadata->description = trim(strip_tags($crawler->filter('.txt.introduction')->text('', false)));
        $metadata->image = $crawler->filter('meta[property="og:image"]')->attr('content');

        // 作品に設定されているジャンルをトリム後に重複排除し、昇順ソートしてタグへ設定する
        $genreTexts = $crawler->filterXPath('//div[@class="detail_data"]//th[text()="ジャンル："]/../td/a')->each(function ($node) {
            return trim($node->text());
        });
        $genreTexts = array_values(array_unique($genreTexts));
        sort($genreTexts);
        $metadata->tags = $genreTexts;

        return $metadata;
    }
}
