<?php

namespace App\MetadataResolver;

use Carbon\Carbon;
use GuzzleHttp\Client;

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
        $metadata = $this->ogpResolver->parse((string) $res->getBody());

        // 画像URLから有効期限の起点を拾う
        parse_str(parse_url($metadata->image, PHP_URL_QUERY), $params);
        if (empty($params['px-time'])) {
            throw new \RuntimeException('Parameter "px-time" not found. Image=' . $metadata->image . ' Source=' . $url);
        }
        $metadata->expires_at = Carbon::createFromTimestamp($params['px-time'])->addHour(1);

        return $metadata;
    }
}
