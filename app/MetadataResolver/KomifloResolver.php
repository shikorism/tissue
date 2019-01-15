<?php

namespace App\MetadataResolver;

class KomifloResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        if (preg_match('~komiflo\.com(?:/#!)?/comics/(\\d+)~', $url, $matches) !== 1) {
            throw new \RuntimeException("Unmatched URL Pattern: $url");
        }
        $id = $matches[1];

        $client = new \GuzzleHttp\Client();
        $res = $client->get('https://api.komiflo.com/content/id/' . $id);
        if ($res->getStatusCode() === 200) {
            $json = json_decode($res->getBody()->getContents(), true);
            $metadata = new Metadata();

            $metadata->title = $json['content']['data']['title'] ?? '';
            $metadata->description = ($json['content']['attributes']['artists']['children'][0]['data']['name'] ?? '?') .
                ' - ' .
                ($json['content']['parents'][0]['data']['title'] ?? '?');
            $metadata->image = $json['content']['cdn_public'] . "/564_mobile_large_3x/" . $json['content']['named_imgs']['cover']['filename'] . $json['content']['signature'];
            $metadata->expires_at = date('Y-m-d H:i:s', strtotime($json['content']['signature_expires']));

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
