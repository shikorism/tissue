<?php

namespace App\MetadataResolver;

class NijieResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);
            $dataNode = $xpath->query('//script[substring(@type, string-length(@type) - 3, 4) = "json"]');
            foreach ($dataNode as $node) {
                // 改行がそのまま入っていることがあるのでデコード前にエスケープが必要
                $imageData = json_decode(preg_replace('/\r?\n/', '\n', $node->nodeValue), true);
                if (isset($imageData['thumbnailUrl'])) {
                    $metadata->image = preg_replace('~nijie\\.info/.*/nijie_picture/~', 'nijie.info/nijie_picture/', $imageData['thumbnailUrl']);
                    break;
                }
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}