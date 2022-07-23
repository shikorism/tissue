<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class TwitterOGPResolver implements TwitterResolver
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
        $url = preg_replace('/(www\.)?(mobile|m)\.twitter\.com/u', 'twitter.com', $url);

        $res = $this->client->get($url);
        $html = (string) $res->getBody();

        return $this->ogpResolver->parse($html);
    }
}
