<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class XtubeResolver implements Resolver
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
        if (preg_match('~www\.xtube\.com/video-watch/.*-(\d+)$~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }
        $videoid = $matches[1];

        $res = $this->client->get('https://www.xtube.com/webmaster/api/getvideobyid?video_id=' . $videoid);
        if ($res->getStatusCode() === 200) {
            $data = json_decode($res->getBody()->getContents(), true);
            $metadata = new Metadata();

            $metadata->title = $data['title'] ?? '';
            $metadata->description = strip_tags(str_replace('\n', PHP_EOL, html_entity_decode($data['description'] ?? '')));
            $metadata->image = str_replace('eSuQ8f', 'eSK08f', $data['thumb'] ?? ''); // 300x169 to 300x210
            $metadata->tags = array_values(array_unique($data['tags']));

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
