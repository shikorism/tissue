<?php
declare(strict_types=1);

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class BoothResolver implements Resolver
{
    public function __construct(private Client $client)
    {
    }

    public function resolve(string $url): Metadata
    {
        if (preg_match('~booth\.pm/(?:[a-z]+/)?items/([0-9]+)~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }
        $id = $matches[1];

        $res = $this->client->get('https://booth.pm/ja/items/' . $id . '.json');
        $json = json_decode($res->getBody()->getContents(), true);

        $metadata = new Metadata();
        $metadata->title = $json['name'];
        $metadata->description = $json['shop']['name'] . "\n\n" . $json['description'];
        if (!empty($json['images'])) {
            $metadata->image = $json['images'][0]['original'];
        }

        $tags = [];

        // カテゴリ・サブカテゴリ
        $tags[] = $json['category']['parent']['name'];
        $tags[] = $json['category']['name'];

        // ショップ
        $tags[] = str_replace(' ', '_', $json['shop']['name']);

        // タグ
        foreach ($json['tags'] as $tag) {
            $tags[] = str_replace(' ', '_', $tag['name']);
        }

        $tags = array_values(array_unique($tags));
        sort($tags);
        $metadata->tags = $tags;

        return $metadata;
    }
}
