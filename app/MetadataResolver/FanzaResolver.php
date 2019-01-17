<?php

namespace App\MetadataResolver;

class FanzaResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());
            $metadata->image =  preg_replace("~(pr|ps)\.jpg$~", "pl.jpg", $metadata->image);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
