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

        // 画像URLのJWTから有効期限を拾う
        parse_str(parse_url($metadata->image, PHP_URL_QUERY), $params);
        if (empty($params['jwt'])) {
            throw new \RuntimeException('Parameter "jwt" not found. Image=' . $metadata->image . ' Source=' . $url);
        }
        $parts = explode('.', $params['jwt']);
        if (count($parts) !== 3) {
            throw new \RuntimeException('Invalid jwt. Image=' . $metadata->image . ' Source=' . $url);
        }
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

        $metadata->expires_at = Carbon::createFromTimestamp($payload['exp']);

        return $metadata;
    }
}
