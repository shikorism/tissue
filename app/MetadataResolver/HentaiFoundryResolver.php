<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class HentaiFoundryResolver implements Resolver
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

    private function nbsp2space(string $string): string
    {
        return str_replace("\xc2\xa0", ' ', $string);
    }

    private function br2nl(string $string): string
    {
        return str_replace('<br>', PHP_EOL, $string);
    }

    public function resolve(string $url): Metadata
    {
        $res = $this->client->get(
            http_build_url($url, ['query' => 'enterAgree=1']),
            ['cookies' => new CookieJar()]
        );

        $metadata = new Metadata();
        $crawler = new Crawler((string) $res->getBody());

        $author =  $crawler->filter('#picBox .boxtitle a')->text();
        $description = trim(strip_tags($this->nbsp2space($this->br2nl($crawler->filter('.picDescript')->html()))));

        $metadata->title = $crawler->filter('#picBox .boxtitle .imageTitle')->text();
        $metadata->description = 'by ' . $author . PHP_EOL . $description;
        $metadata->image = 'https:' . $crawler->filter('img[src^="//picture"]')->attr('src');
        $metadata->tags = $crawler->filter('a[rel="tag"]')->extract(['_text']);

        return $metadata;
    }
}
