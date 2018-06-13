<?php

namespace App\MetadataResolver;

use GuzzleHttp\Cookie\CookieJar;

class MelonbooksResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['AUTH_ADULT' => '1'], 'www.melonbooks.co.jp');

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