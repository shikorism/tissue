<?php

namespace App\MetadataResolver;

use GuzzleHttp\Cookie\CookieJar;

class NarouResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['over18' => 'yes'], '.syosetu.com');

        $client = new \GuzzleHttp\Client();
        $res = $client->get($url, ['cookies' => $cookieJar]);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();

            return $ogpResolver->parse($res->getBody());
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
