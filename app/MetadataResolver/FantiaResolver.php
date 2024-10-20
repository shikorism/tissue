<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class FantiaResolver implements Resolver
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
        preg_match("~posts/(\d+)~", $url, $match);
        $postId = $match[1];

        $res = $this->client->get("https://fantia.jp/api/v1/posts/{$postId}", [
            'headers' => [
                'X-Requested-With' => 'XMLHttpRequest'
            ]
        ]);
        $data = json_decode(str_replace('\r\n', '\n', (string) $res->getBody()), true);
        $post = $data['post'];

        $tags = array_map(function ($tag) {
            return $tag['name'];
        }, $post['tags']);

        $metadata = new Metadata();
        $metadata->title = $post['title'] ?? '';
        $metadata->description = 'サークル: ' . $post['fanclub']['fanclub_name_with_creator_name'] . PHP_EOL . $post['comment'];
        $metadata->image = str_replace('micro', 'main', $post['thumb_micro']) ?? '';
        $metadata->tags = array_merge($tags, [$post['fanclub']['creator_name']]);

        return $metadata;
    }
}
