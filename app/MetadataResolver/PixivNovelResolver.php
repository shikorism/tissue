<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

/**
 * Resolver for pixiv.net/novel/show.php, NOT novel.pixiv.net
 */
class PixivNovelResolver implements Resolver
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
        preg_match('~www\.pixiv\.net/novel/show\.php\?id=(?P<novelId>\d+)~', $url, $matches);
        $novelId = $matches['novelId'];

        $metadata = $this->ogpResolver->resolve($url);

        $res = $this->client->get('https://www.pixiv.net/ajax/novel/' . $novelId);
        $json = json_decode($res->getBody()->getContents(), true);

        $metadata->title = $json['body']['title'];
        $metadata->description = '投稿者: ' . $json['body']['userName'] . PHP_EOL . strip_tags(str_replace('<br />', PHP_EOL, $json['body']['description'] ?? ''));

        if (!empty($json['body']['tags']['tags'])) {
            foreach ($json['body']['tags']['tags'] as $tag) {
                // サイトの性質上不要なキーワードは無視
                if ($tag['tag'] !== 'R-18') {
                    $metadata->tags[] = preg_replace('/\s/', '_', $tag['tag']);
                }
            }
        }

        return $metadata;
    }
}
