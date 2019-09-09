<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class FanzaResolver implements Resolver
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
        $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $metadata->image);
        $metadata->description = str_replace('<>', '', $metadata->description);

        return $metadata;
    }
}
