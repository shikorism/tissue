<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class PixivResolver implements Resolver
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

    /**
     * サムネイル画像 URL から最大長辺 1200px の画像 URL に変換する
     *
     * @param string $thumbnailUrl サムネイル画像 URL
     *
     * @return string 1200px の画像 URL
     */
    public function thumbnailToMasterUrl(string $thumbnailUrl): string
    {
        $temp = str_replace('/c/128x128', '', $thumbnailUrl);
        $largeUrl = str_replace('square1200.jpg', 'master1200.jpg', $temp);

        return $largeUrl;
    }

    /**
     * 直リン可能な pixiv.cat のプロキシ URL に変換する
     * HUGE THANKS TO PIXIV.CAT!
     *
     * @param string $pixivUrl i.pximg URL
     *
     * @return string i.pixiv.cat URL
     */
    public function proxize(string $pixivUrl): string
    {
        return str_replace('i.pximg.net', 'i.pixiv.cat', $pixivUrl);
    }

    /**
     * HTMLからタグとして利用可能な情報を抽出する
     * @param string $html ページ HTML
     * @return string[] タグ
     */
    public function extractTags(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $nodes = $xpath->query("//meta[@name='keywords']");
        if ($nodes->length === 0) {
            return [];
        }

        $keywords = $nodes->item(0)->getAttribute('content');
        $tags = [];

        foreach (mb_split(',', $keywords) as $keyword) {
            $keyword = trim($keyword);

            if (empty($keyword)) {
                continue;
            }

            // 一部の固定キーワードは無視
            if (array_search($keyword, ['R-18', 'イラスト', 'pixiv', 'ピクシブ'], true) !== false) {
                continue;
            }

            $tags[] = preg_replace('/\s/', '_', $keyword);
        }

        return $tags;
    }

    public function resolve(string $url): Metadata
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $illustId = $params['illust_id'];
        $page = 0;

        // 漫画ページ（ページ数はmanga_bigならあるかも）
        if ($params['mode'] === 'manga_big' || $params['mode'] === 'manga') {
            $page = $params['page'] ?? 0;

            // 未ログインでは漫画ページを開けないため、URL を作品ページに変換する
            $url = preg_replace('~mode=manga(_big)?~', 'mode=medium', $url);
        }

        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            preg_match("~https://i\.pximg\.net/c/128x128/img-master/img/\d{4}/\d{2}/\d{2}/\d{2}/\d{2}/\d{2}/{$illustId}(_p0)?_square1200\.jpg~", $res->getBody(), $match);
            $illustThumbnailUrl = $match[0];

            if ($page != 0) {
                $illustThumbnailUrl = str_replace('_p0', '_p'.$page, $illustThumbnailUrl);
            }

            $illustUrl = $this->thumbnailToMasterUrl($illustThumbnailUrl);

            $metadata->image = $this->proxize($illustUrl);

            $metadata->tags = $this->extractTags($res->getBody());

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
