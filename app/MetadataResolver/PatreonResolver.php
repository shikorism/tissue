<?php

namespace App\MetadataResolver;

use Carbon\Carbon;

class PatreonResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());

            parse_str(parse_url($metadata->image)["query"], $temp);
            $expires_at_unixtime = $temp["token-time"];
            $expires_at = Carbon::createFromTimestamp($expires_at_unixtime);

            $metadata->expires_at = $expires_at;

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
