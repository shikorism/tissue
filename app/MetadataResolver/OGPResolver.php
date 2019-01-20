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
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win'));
        $xpath = new \DOMXPath($dom);

        $metadata = new Metadata();

        $metadata->title = $this->findContent($xpath, '//meta[@*="og:title"]', '//meta[@*="twitter:title"]');
        $metadata->description = $this->findContent($xpath, '//meta[@*="og:description"]', '//meta[@*="twitter:description"]');
        $metadata->image = $this->findContent($xpath, '//meta[@*="og:image"]', '//meta[@*="twitter:image"]');

        return $metadata;
    }

    private function findContent(\DOMXPath $xpath, string ...$expressions)
    {
        foreach ($expressions as $expression) {
            $nodes = $xpath->query($expression);
            foreach ($nodes as $node) {
                $content = $node->getAttribute('content');
                if (!empty($content)) {
                    return $content;
                }
            }
        }

        return '';
    }
}
