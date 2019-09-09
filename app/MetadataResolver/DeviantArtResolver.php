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
        $data = json_decode($res->getBody()->getContents(), true);
        $metadata = new Metadata();

        // アスペクト比を保ったまま、縦か横が最大1024pxになる画像を取得する。
        // Ref: https://support.wixmp.com/en/article/image-service-3835799
        // 作成されていない画像が参照されると403を返すようなので、サイト内で使用されている1024pxにした。
        $metadata->image  = preg_replace('~/v1/fit/w_\d+,h_\d+(?:,q_\d+),strp/.+\.(jpg|png|webp|gif)~', '/v1/fit/w_1024,h_1024,strp/image.jpg', $data['thumbnail_url']);
        $metadata->title = $data['title'] ?? '';
        $metadata->description = 'By ' . $data['author_name'];

        return $metadata;
    }
}
