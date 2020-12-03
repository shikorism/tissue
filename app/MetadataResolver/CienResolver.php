<?php

namespace App\MetadataResolver;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class CienResolver extends MetadataResolver
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
        $res = $this->client->get($url);
        $html = (string) $res->getBody();
        $metadata = $this->ogpResolver->parse($html);
        $crawler = new Crawler($html);

        // OGPのデフォルトはバナーなので、投稿に使える画像があればそれを使う
        $selector = 'img[data-actual*="image-web"]';
        if ($crawler->filter($selector)->count() !== 0) {
            $metadata->image = $crawler->filter($selector)->attr('data-actual');
        }

        // JWTがついていれば画像URLのJWTから有効期限を拾う
        parse_str(parse_url($metadata->image, PHP_URL_QUERY), $params);
        if (isset($params['jwt'])) {
            $parts = explode('.', $params['jwt']);
            if (count($parts) !== 3) {
                throw new \RuntimeException('Invalid jwt. Image=' . $metadata->image . ' Source=' . $url);
            }
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

            $metadata->expires_at = Carbon::createFromTimestamp($payload['exp']);
        }

        return $metadata;
    }
}
