<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class NijieResolver implements Resolver
{
    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function resolve(string $url): Metadata
    {
        if (mb_strpos($url, '//sp.nijie.info') !== false) {
            $url = preg_replace('~//sp\.nijie\.info~', '//nijie.info', $url);
        }
        if (mb_strpos($url, 'view_popup.php') !== false) {
            $url = preg_replace('~view_popup\.php~', 'view.php', $url);
        }

        $client = $this->client;
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
                if (isset($imageData['thumbnailUrl']) && !ends_with($imageData['thumbnailUrl'], '.gif') && !ends_with($imageData['thumbnailUrl'], '.mp4')) {
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
