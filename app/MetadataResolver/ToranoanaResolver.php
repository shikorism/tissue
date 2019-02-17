<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class ToranoanaResolver implements Resolver
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
        $cookieJar = CookieJar::fromArray(['adflg' => '0'], 'ec.toranoana.jp');

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        if ($res->getStatusCode() === 200) {

            return $this->ogpResolver->parse($res->getBody());
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
