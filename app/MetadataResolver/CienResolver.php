<?php

namespace App\MetadataResolver;

use Carbon\Carbon;

class CienResolver extends MetadataResolver
{
    public function resolve(string $url): Metadata
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());

            // 画像URLから有効期限の起点を拾う
            parse_str(parse_url($metadata->image, PHP_URL_QUERY), $params);
            if (empty($params['px-time'])) {
                throw new \RuntimeException('Parameter "px-time" not found. Image=' . $metadata->image . ' Source=' . $url);
            }
            $metadata->expires_at = Carbon::createFromTimestamp($params['px-time'])->addHour(1);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
