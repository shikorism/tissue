<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class FC2ContentsResolver implements Resolver
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

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $thumbnailNode = $xpath->query('//*[@class="main_thum_img"]/a')->item(0);
            if ($thumbnailNode) {
                $metadata->image = preg_replace('~^http:~', 'https:', $thumbnailNode->getAttribute('href'));
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
