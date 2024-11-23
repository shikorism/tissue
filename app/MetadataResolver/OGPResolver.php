<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;

class OGPResolver implements Resolver, Parser
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
        return $this->parse($this->client->get($url, [RequestOptions::COOKIES => new CookieJar()])->getBody());
    }

    public function parse(string $html, ?OGPParsePriority $priority = null): Metadata
    {
        if ($priority === null) {
            $priority = OGPParsePriority::preferTo(OGPParsePriority::OGP);
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($html, 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win'));
        $xpath = new \DOMXPath($dom);

        $metadata = new Metadata();

        $metadata->title = $this->findContent($xpath, ...$priority->sortForTitle('//meta[@*="og:title"]', '//meta[@*="twitter:title"]'));
        if (empty($metadata->title)) {
            $nodes = $xpath->query('//title');
            if ($nodes->length !== 0) {
                $metadata->title = $nodes->item(0)->textContent;
            }
        }
        $metadata->description = $this->findContent(
            $xpath,
            ...$priority->sortForDescription('//meta[@*="og:description"]', '//meta[@*="twitter:description"]', '//meta[@name="description"]')
        );
        $metadata->image = $this->findContent($xpath, ...$priority->sortForImage('//meta[@*="og:image"]', '//meta[@*="twitter:image"]'));

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
