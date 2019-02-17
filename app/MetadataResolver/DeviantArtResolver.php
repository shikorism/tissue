<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class DeviantArtResolver implements Resolver
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

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $node = $xpath->query('//*[@id="pimp-preload"]/following-sibling::div//img')->item(0);
            $srcset = $node->getAttribute('srcset');
            $srcset_array = explode('w,', $srcset);
            $src = end($srcset_array);
            $src = preg_replace('~ \d+w$~', '', $src);

            if (preg_match('~\.wixmp\.com$~', parse_url($src)['host'])) {
                // アスペクト比を保ったまま、縦か横が最大700pxになるように変換する。
                // Ref: https://support.wixmp.com/en/article/image-service-3835799
                if (strpos($src, '/v1/fill/')) {
                    $src = preg_replace('~/v1/fill/w_\d+,h_\d+,q_\d+,strp~', '/v1/fit/w_700,h_700,q_70,strp', $src);
                } else {
                    $src = $src . '/v1/fit/w_700,h_700,q_70,strp/image.jpg';
                }
            }

            $metadata->image = $src;

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
