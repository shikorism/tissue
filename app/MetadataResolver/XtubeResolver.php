<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class XtubeResolver implements Resolver
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
        if (preg_match('~www\.xtube\.com/video-watch/.*-(\d+)$~', $url) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }

        $res = $this->client->get($url);
        $html = (string) $res->getBody();
        $metadata = new Metadata();
        $crawler = new Crawler($html);

        // poster URL抽出
        $playerConfig = explode("\n", trim($crawler->filter('#playerWrapper script')->last()->text()));
        preg_match('~https:\\\/\\\/cdn\d+-s-hw-e5\.xtube\.com\\\/m=(?P<size>.{8})\\\/videos\\\/\d{6}\\\/\d{2}\\\/.{5}-.{4}-\\\/original\\\/\d+\.jpg~', $playerConfig[0], $matches);
        $metadata->image = str_replace('\/', '/', $matches[0]);

        $metadata->title = trim($crawler->filter('.underPlayerRateForm h1')->text(''));
        $metadata->description =  trim($crawler->filter('.fullDescription ')->text(''));
        $metadata->tags = $crawler->filter('.tagsCategories a')->extract('_text');

        return $metadata;
    }
}
