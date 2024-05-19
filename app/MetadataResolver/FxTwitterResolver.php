<?php
declare(strict_types=1);

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class FxTwitterResolver implements TwitterResolver
{
    private Client $client;
    private OGPResolver $ogpResolver;

    public function __construct(Client $client, OGPResolver $ogpResolver)
    {
        $this->client = $client;
        $this->ogpResolver = $ogpResolver;
    }

    public function resolve(string $url): Metadata
    {
        $url = preg_replace('/(www\.)?((mobile|m)\.)?(twitter|x)\.com/u', 'fxtwitter.com', $url);

        $res = $this->client->get($url);
        $html = (string) $res->getBody();

        return $this->ogpResolver->parse($html);
    }
}
