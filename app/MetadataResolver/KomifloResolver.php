<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class KomifloResolver implements Resolver
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function resolve(string $url): Metadata
    {
        if (preg_match('~komiflo\.com(?:/#!)?/comics/(\\d+)~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }
        $id = $matches[1];

        $res = $this->client->get('https://api.komiflo.com/content/id/' . $id);
        $json = json_decode($res->getBody()->getContents(), true);
        $metadata = new Metadata();

        $metadata->title = $json['content']['data']['title'] ?? '';
        $metadata->description = ($json['content']['attributes']['artists']['children'][0]['data']['name'] ?? '?') .
            ' - ' . ($json['content']['parents'][0]['data']['title'] ?? '?');
        $metadata->image = 'https://t.komiflo.com/564_mobile_large_3x/' . $json['content']['named_imgs']['cover']['filename'];

        // 作者情報
        if (!empty($json['content']['attributes']['artists']['children'])) {
            foreach ($json['content']['attributes']['artists']['children'] as $artist) {
                $metadata->tags[] = preg_replace('/\s/', '_', $artist['data']['name']);
            }
        }

        // タグ
        if (!empty($json['content']['attributes']['tags']['children'])) {
            $tags = [];
            foreach ($json['content']['attributes']['tags']['children'] as $tag) {
                $tags[] = preg_replace('/\s/', '_', $tag['data']['name']);
            }
            sort($tags);
            $metadata->tags = array_merge($metadata->tags, $tags);
        }

        return $metadata;
    }
}
