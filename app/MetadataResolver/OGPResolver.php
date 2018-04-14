<?php

namespace App\MetadataResolver;

class OGPResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            return $this->parse($res->getBody());
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }

    public function parse(string $html): Metadata
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $metadata = new Metadata();

        $titleNode = $xpath->query('//meta[@*="og:title"]');
        foreach ($titleNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $metadata->title = $node->getAttribute('content');
                break;
            }
        }

        $descriptionNode = $xpath->query('//meta[@*="og:description"]');
        foreach ($descriptionNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $metadata->description = $node->getAttribute('content');
                break;
            }
        }

        $imageNode = $xpath->query('//meta[@*="og:image"]');
        foreach ($imageNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $metadata->image = $node->getAttribute('content');
                break;
            }
        }

        return $metadata;
    }
}