<?php

namespace App\MetadataResolver;

class PixivResolver implements Resolver
{
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

        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());

            preg_match("~https://i\.pximg\.net/c/128x128/img-master/img/\d{4}/\d{2}/\d{2}/\d{2}/\d{2}/\d{2}/{$illustId}(_p0)?_square1200\.jpg~", $res->getBody(), $match);
            $illustThumbnailUrl = $match[0];

            if ($page != 0) {
                $illustThumbnailUrl = str_replace('_p0', '_p'.$page, $illustThumbnailUrl);
            }

            $illustUrl = $this->thumbnailToMasterUrl($illustThumbnailUrl);

            $metadata->image = $this->proxize($illustUrl);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
