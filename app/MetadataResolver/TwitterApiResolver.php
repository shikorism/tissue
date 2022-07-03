<?php
declare(strict_types=1);

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class TwitterApiResolver implements Resolver
{
    public function __construct(private Client $client)
    {
    }

    public function resolve(string $url): Metadata
    {
        if (preg_match('~(?:www\.)?(?:(?:mobile|m)\.)?twitter\.com/(?:#!/)?[0-9a-zA-Z_]{1,15}/status(?:es)?/(?P<id>[0-9]+)/?(?:\\?.+)?$~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }

        $res = $this->client->get('https://api.twitter.com/1.1/statuses/show.json', [
            'headers' => ['Authorization' => 'Bearer ' . $_ENV['TWITTER_API_BEARER_TOKEN']],
            'query' => ['id' => $matches['id'], 'tweet_mode' => 'extended'],
        ]);
        $json = json_decode($res->getBody()->getContents(), true, flags: JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);

        $metadata = new Metadata();
        $metadata->title = $json['user']['name'];
        $metadata->description = $json['full_text'];

        // Metadataに保存可能なのは1枚の画像のみなので、動画等の情報を含む可能性があるextended_entitiesよりもentitiesから取ったほうが都合が良い
        if (!empty($json['entities']['media'])) {
            $media = $json['entities']['media'][0];
            $metadata->image = $media['media_url_https'];
        }

        return $metadata;
    }
}
