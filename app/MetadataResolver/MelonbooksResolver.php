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
            $metadata = $ogpResolver->parse($res->getBody());

            // censoredフラグの除去
            if (mb_strpos($metadata->image, '&c=1') !== false) {
                $metadata->image = preg_replace('/&c=1/u', '', $metadata->image);
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}