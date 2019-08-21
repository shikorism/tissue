<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class IwaraResolver implements Resolver
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
        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = new Metadata();
            $html = (string) $res->getBody();
            $crawler = new Crawler($html);

            $infoElements = $crawler->filter('#video-player + div, .field-name-field-video-url + div, .field-name-field-images + div');
            $title = $infoElements->filter('h1.title')->text();
            $author = $infoElements->filter('.username')->text();
            $description = '';
            if ($infoElements->filter('.field-type-text-with-summary')->count()) {
                $description = $infoElements->filter('.field-type-text-with-summary')->text();
            }
            $tags =  $infoElements->filter('a[href^="/video-categories"], a[href^="/images"]')->extract('_text');
            // 役に立たないタグを削除する
            $tags = array_values(array_diff($tags, ['Uncategorized', 'Other']));
            array_push($tags, $author);

            $metadata->title = $title;
            $metadata->description = '投稿者: ' . $author . PHP_EOL . $description;
            $metadata->tags = $tags;

            // iwara video
            if ($crawler->filter('#video-player')->count()) {
                $metadata->image = 'https:' . $crawler->filter('#video-player')->attr('poster');
            }

            // youtube
            if ($crawler->filter('iframe[src^="//www.youtube.com"]')->count()) {
                if (preg_match('~youtube\.com/embed/(\S+)\?~', $crawler->filter('iframe[src^="//www.youtube.com"]')->attr('src'), $matches) === 1) {
                    $youtubeId = $matches[1];
                    $metadata->image = 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg';
                }
            }

            // images
            if ($crawler->filter('.field-name-field-images')->count()) {
                $metadata->image = 'https:' . $crawler->filter('.field-name-field-images a')->first()->attr('href');
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
