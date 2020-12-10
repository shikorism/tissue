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
        if (preg_match('~www\.xtube\.com/video-watch/.*-(\d+)$~', $url) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }

        $res = $this->client->get($url);
        $html = (string) $res->getBody();
        $metadata = $this->ogpResolver->parse($html);
        $crawler = new Crawler($html);

        $metadata->title = trim($crawler->filter('.underPlayerRateForm h1')->text(''));
        // $metadata->description =  trim($crawler->filter('.fullDescription ')->text(''));
        $metadata->image = str_replace('m=eSuQ8f', 'm=eaAaaEFb', $metadata->image);
        $metadata->image = str_replace('240X180', 'original', $metadata->image);
        $metadata->tags = array_map('trim', $crawler->filter('.tagsCategories a')->extract('_text'));

        return $metadata;
    }
}
