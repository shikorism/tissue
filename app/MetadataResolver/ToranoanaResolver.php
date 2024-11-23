<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class ToranoanaResolver implements Resolver
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
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($res->getBody(), 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        $imgNode = $xpath->query('//*[@id="preview"]//img')->item(0);
        if ($imgNode !== null) {
            $metadata->image = $imgNode->getAttribute('src');
        }

        return $metadata;
    }
}
