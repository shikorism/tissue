<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class SteamResolver implements Resolver
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
        if (preg_match('~store\.steampowered\.com/app/(\d+)~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }
        $appid = $matches[1];

        $res = $this->client->get('https://store.steampowered.com/api/appdetails/?l=japanese&appids=' . $appid);
        if ($res->getStatusCode() === 200) {
            $json = json_decode($res->getBody()->getContents(), true);
            if ($json[$appid]['success'] === false) {
                throw new \RuntimeException("API response [$appid][success] is false: $url");
            }
            $data = $json[$appid]['data'];
            $metadata = new Metadata();

            $metadata->title = $data['name'] ?? '';
            $metadata->description = strip_tags(str_replace('<br />', PHP_EOL, html_entity_decode($data['short_description'] ?? '')));
            $metadata->image = $data['header_image'] ?? '';

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
