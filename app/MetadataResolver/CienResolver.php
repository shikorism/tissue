<?php

namespace App\MetadataResolver;

use Carbon\Carbon;
use GuzzleHttp\Client;

class CienResolver implements Resolver
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

        // パラメータにpx-timeがついていればpx-timeから有効期限を設定する
        parse_str(parse_url($metadata->image, PHP_URL_QUERY), $params);
        if (isset($params['px-time'])) {
            $metadata->expires_at = Carbon::createFromTimestamp($params['px-time'])->addHour(1);
        }

        return $metadata;
    }
}
