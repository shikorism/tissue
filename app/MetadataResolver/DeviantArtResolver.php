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
        $res = $this->client->get('https://backend.deviantart.com/oembed?url=' . $url);
        if ($res->getStatusCode() === 200) {
            $data = json_decode($res->getBody()->getContents(), true);
            $metadata = new Metadata();

            if (preg_match('~\.wixmp\.com$~', parse_url($data['url'])['host'])) {
                // アスペクト比を保ったまま、縦か横が最大700pxになるように変換する。
                // Ref: https://support.wixmp.com/en/article/image-service-3835799
                if (strpos($data['url'], '/v1/fill/')) {
                    $metadata->image  = preg_replace('~/v1/fill/w_\d+,h_\d+,q_\d+,strp~', '/v1/fit/w_700,h_700,q_70,strp', $data['url']);
                } else {
                    $queryStartPos = strpos($data['url'], '?');
                    $metadata->image = substr_replace($data['url'], '/v1/fit/w_700,h_700,strp/image.jpg', $queryStartPos, 0);
                }
            } else {
                $metadata->image = $data['url'];
            }

            $metadata->title = $data['title'] ?? '';
            $metadata->description = 'By ' . $data['author_name'];

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
