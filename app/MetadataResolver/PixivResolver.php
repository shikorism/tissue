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

    /**
     * 作品が漫画かイラストか判定する
     *
     * @param string $html ページ HTML
     * @return bool 漫画ならtrue
     */
    public function isManga(string $html): bool
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $nodes = $xpath->query("//meta[@name='description']");
        if ($nodes->length === 0) {
            throw new \RuntimeException("meta[@name='description']が見つからなかった: $url");
        }

        $description = $nodes->item(0)->getAttribute('content');
        logger($description);
        $isManga = false;

        if (preg_match('~さんの漫画です。 「.+」~', $description)) {
            $isManga = true;
        }

        return $isManga;
    }

    /**
     * 直リン可能な pixiv.cat のプロキシURLを作成する
     * HUGE THANKS TO PIXIV.CAT!
     *
     * @param int    $illustId
     * @param string $illustExt イラストの拡張子
     * @param bool   $isManga 作品が漫画かどうか
     * @param int    $page 参照するページ
     * @return string i.pixiv.cat URL
     */
    public function createProxyURL(int $illustId, string $illustExt = jpg, bool $isManga = false, int $page = 0): string
    {
        // 拡張子はjpg png gifの場合があるが、わざわざ判別するためにjson読むのもめんどくさいしjpgで問題ないのでjpgにしている
        $url = 'https://pixiv.cat/' . $illustId . '.' . $illustExt;
        if ($isManga) {
            // pixiv.cat では 1 からページを数えるため +1 する
            $page = $page +1;
            $url = 'https://pixiv.cat/' . $illustId . '-' . $page . '.' . $illustExt;
        }

        return $url;
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

            $isManga = $this->isManga($res->getBody());

            $metadata->image = $this->createProxyURL($illustId, 'jpg', $isManga, $page);
            $metadata->tags = $this->extractTags($res->getBody());

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
