<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
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

        $curlopt = [];
        if (!str_contains(curl_version()['ssl_version'], 'NSS/')) {
            // OpenSSLを使用している場合、SECLEVELを下げて接続する
            $curlopt[CURLOPT_SSL_CIPHER_LIST] = 'DEFAULT@SECLEVEL=1';
        }
        $res = $this->client->get($url, [
            'cookies' => $cookieJar,
            'curl' => $curlopt
        ]);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($res->getBody(), 'UTF-8'));
        $xpath = new \DOMXPath($dom);
        $descriptionNodelist = $xpath->query('//div[contains(@class, "item-detail")]/*[contains(@class, "page-headline") and contains(text(), "作品詳細")]/following-sibling::div[1]');
        $specialDescriptionNodelist = $xpath->query('//div[contains(@class, "item-detail")]/*[contains(@class, "page-headline") and contains(text(), "スタッフのオススメポイント")]/following-sibling::div[1]');

        // censoredフラグの除去
        if (mb_strpos($metadata->image, '&c=1') !== false) {
            $metadata->image = preg_replace('/&c=1/u', '', $metadata->image);
        }

        // 抽出
        preg_match('~^(.+)\((.+)\)の通販・購入はメロンブックス~', $metadata->title, $match);
        $title = $match[1];
        $maker = $match[2];

        // 整形
        $description = 'サークル: ' . $maker . "\n";

        if ($descriptionNodelist->length !== 0) {
            $description .= trim(str_replace("\r\n", "\n", $descriptionNodelist->item(0)->textContent)) . "\n\n";
        }

        if ($specialDescriptionNodelist->length !== 0) {
            $description .= trim(str_replace("\r\n", "\n", $specialDescriptionNodelist->item(0)->textContent));
        }

        $metadata->title = $title;
        $metadata->description = trim($description);

        return $metadata;
    }
}
