<?php

namespace App\MetadataResolver;

class NicoSeigaResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());

            // ページURLからサムネイルURLに変換
            preg_match('~http://(?:(?:sp\\.)?seiga\\.nicovideo\\.jp/seiga(?:/#!)?|nico\\.ms)/im(\\d+)~', $url, $matches);
            $metadata->image = "http://lohas.nicoseiga.jp/thumb/${matches[1]}l?";

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}