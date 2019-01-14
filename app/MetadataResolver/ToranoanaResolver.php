<?php

namespace App\MetadataResolver;

use GuzzleHttp\Cookie\CookieJar;

class ToranoanaResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['adflg' => '0'], 'ec.toranoana.jp');

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
