<?php
declare(strict_types=1);

namespace App\MetadataResolver;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterApiResolver implements Resolver
{
    private TwitterOAuth $twitter;

    public function __construct(TwitterOAuth $twitter)
    {
        $this->twitter = $twitter;
    }

    public function resolve(string $url): Metadata
    {
        if (preg_match('~(?:www\.)?(?:(?:mobile|m)\.)?twitter\.com/(?:#!/)?[0-9a-zA-Z_]{1,15}/status(?:es)?/(?P<id>[0-9]+)/?(?:\\?.+)?$~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }

        $res = $this->twitter->get('statuses/show', ['id' => $matches['id'], 'tweet_mode' => 'extended']);

        if ($this->twitter->getLastHttpCode() !== 200) {
            throw new \RuntimeException("{$this->twitter->getLastHttpCode()}: $url");
        }

        $metadata = new Metadata();
        $metadata->title = $res->user->name;
        $metadata->description = $res->full_text;

        // Metadataに保存可能なのは1枚の画像のみなので、動画等の情報を含む可能性があるextended_entitiesよりもentitiesから取ったほうが都合が良い
        if (!empty($res->entities->media)) {
            $media = $res->entities->media[0];
            $metadata->image = $media->media_url_https;
        }

        return $metadata;
    }
}
