<?php

namespace App\MetadataResolver;

class IwaraResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            $metadata = new Metadata();

            // find title
            foreach ($xpath->query('//title') as $node) {
                $content = $node->textContent;
                if (!empty($content)) {
                    $metadata->title = $content;
                    break;
                }
            }

            // find thumbnail
            foreach ($xpath->query('//*[@id="video-player"]') as $node) {
                $poster = $node->getAttribute('poster');
                if (!empty($poster)) {
                    if (strpos($poster, '//') === 0) {
                        $poster = 'https:' . $poster;
                    }
                    $metadata->image = $poster;
                    break;
                }
            }
            if (empty($metadata->image)) {
                // YouTube embedded?
                foreach ($xpath->query('//div[@class="embedded-video"]//iframe') as $node) {
                    $src = $node->getAttribute('src');
                    if (preg_match('~youtube\.com/embed/(\S+)\?~', $src, $matches) !== -1) {
                        $youtubeId = $matches[1];
                        $iwaraThumbUrl = 'https://i.iwara.tv/sites/default/files/styles/thumbnail/public/video_embed_field_thumbnails/youtube/' . $youtubeId . '.jpg';

                        $metadata->image = $iwaraThumbUrl;
                        break;
                    }
                }
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
