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

        $metadata->title = $data['title'] ?? '';
        $metadata->description = 'By ' . $data['author_name'];
        $metadata->image  = $data['url'];
        if (isset($data['tags'])) {
            $metadata->tags = explode(', ', $data['tags']);
        }

        return $metadata;
    }
}
