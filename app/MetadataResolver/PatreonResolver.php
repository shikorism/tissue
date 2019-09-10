<?php

namespace App\MetadataResolver;

use Carbon\Carbon;
use GuzzleHttp\Client;

class PatreonResolver implements Resolver
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

        parse_str(parse_url($metadata->image, PHP_URL_QUERY), $query);
        if (isset($query['token-time'])) {
            $expires_at_unixtime = $query['token-time'];
            $metadata->expires_at = Carbon::createFromTimestamp($expires_at_unixtime);
        }

        return $metadata;
    }
}
