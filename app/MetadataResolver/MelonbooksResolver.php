<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class MelonbooksResolver implements Resolver
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
        $cookieJar = CookieJar::fromArray(['AUTH_ADULT' => '1'], 'www.melonbooks.co.jp');

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

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
