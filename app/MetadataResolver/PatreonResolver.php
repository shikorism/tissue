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
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            parse_str(parse_url($metadata->image, PHP_URL_QUERY), $temp);
            $expires_at_unixtime = $temp['token-time'];
            $expires_at = Carbon::createFromTimestamp($expires_at_unixtime);

            $metadata->expires_at = $expires_at;

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
