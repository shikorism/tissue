<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class ActivityPubResolver implements Resolver, Parser
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $activityClient;

    public function __construct()
    {
        $this->activityClient = new \GuzzleHttp\Client([
            'headers' => [
                'Accept' => 'application/activity+json, application/ld+json; profile="https://www.w3.org/ns/activitystreams"'
            ]
        ]);
    }

    public function resolve(string $url): Metadata
    {
        $res = $this->activityClient->get($url);
        if ($res->getStatusCode() === 200) {
            return $this->parse($res->getBody());
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }

    public function parse(string $json): Metadata
    {
        $activityOrObject = json_decode($json, true);
        $object = $activityOrObject['object'] ?? $activityOrObject;

        if ($object['type'] !== 'Note') {
            throw new UnsupportedContentException('Unsupported object type: ' . $object['type']);
        }

        $metadata = new Metadata();

        $metadata->title = isset($object['attributedTo']) ? $this->getTitleFromActor($object['attributedTo']) : '';
        $metadata->description .= isset($object['summary']) ? $object['summary'] . ' | ' : '';
        $metadata->description .= isset($object['content']) ? $this->html2text($object['content']) : '';
        $metadata->image = $object['attachment'][0]['url'] ?? '';

        return $metadata;
    }

    private function getTitleFromActor(string $url): string
    {
        try {
            $res = $this->activityClient->get($url);
            if ($res->getStatusCode() !== 200) {
                Log::info(self::class . ': Actorの取得に失敗 URL=' . $url);

                return '';
            }

            $actor = json_decode($res->getBody(), true);
            $title = $actor['name'] ?? '';
            if (isset($actor['preferredUsername'])) {
                $title .= ' (@' . $actor['preferredUsername'] . '@' . parse_url($actor['id'], PHP_URL_HOST) . ')';
            }

            return $title;
        } catch (TransferException $e) {
            Log::info(self::class . ': Actorの取得に失敗 URL=' . $url);

            return '';
        }
    }

    private function html2text(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $html = Formatter::htmlEntities($html, 'UTF-8');
        $html = preg_replace('~<br\s*/?\s*>|</p>\s*<p[^>]*>~i', "\n", $html);
        $dom = new \DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        return $dom->textContent;
    }
}
