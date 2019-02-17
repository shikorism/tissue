<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class NicoSeigaResolver implements Resolver
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

            // ページURLからサムネイルURLに変換
            preg_match('~http://(?:(?:sp\\.)?seiga\\.nicovideo\\.jp/seiga(?:/#!)?|nico\\.ms)/im(\\d+)~', $url, $matches);
            $metadata->image = "http://lohas.nicoseiga.jp/thumb/${matches[1]}l?";

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
