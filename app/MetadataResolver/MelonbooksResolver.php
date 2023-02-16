<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class MelonbooksResolver implements Resolver
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
        $cookieJar = CookieJar::fromArray(['AUTH_ADULT' => '1'], 'www.melonbooks.co.jp');

        $res = $this->client->get($url, [
            'cookies' => $cookieJar,
            'curl' => [CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1']
        ]);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        $descriptionNodelist = $xpath->query('//div[@id="description"]//p');
        $specialDescriptionNodelist = $xpath->query('//div[@id="special_description"]//p');

        // censoredフラグの除去
        if (mb_strpos($metadata->image, '&c=1') !== false) {
            $metadata->image = preg_replace('/&c=1/u', '', $metadata->image);
        }

        // 抽出
        preg_match('~^(.+)（(.+)）の通販・購入はメロンブックス~', $metadata->title, $match);
        $title = $match[1];
        $maker = $match[2];

        // 整形
        $description = 'サークル: ' . $maker . "\n";

        if ($specialDescriptionNodelist->length !== 0) {
            $description .= trim(str_replace('<br>', "\n", $specialDescriptionNodelist->item(0)->nodeValue)) . "\n";
            if ($specialDescriptionNodelist->length === 2) {
                $description .= "\n";
                $description .= trim(str_replace('<br>', "\n", $specialDescriptionNodelist->item(1)->nodeValue)) . "\n";
            }
        }

        if ($descriptionNodelist->length !== 0) {
            $description .= trim(str_replace('<br>', "\n", $descriptionNodelist->item(0)->nodeValue));
        }

        $metadata->title = $title;
        $metadata->description = trim($description);

        return $metadata;
    }
}
