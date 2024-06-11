<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Kb10uyShortStoryServerResolver implements Resolver
{
    protected const EXCLUDED_TAGS = ['R-15', 'R-18'];

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
        $res = $this->client->get($url);
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);
        $infoElement = $crawler->filter('div.post-info');

        $metadata = new Metadata();
        $metadata->title = $infoElement->filter('h1')->text();
        $metadata->description = trim($infoElement->filter('p.summary')->text('', false));
        $metadata->tags = array_values(array_diff($infoElement->filter('ul.tags > li.tag > a')->extract(['_text']), self::EXCLUDED_TAGS));

        return $metadata;
    }
}
