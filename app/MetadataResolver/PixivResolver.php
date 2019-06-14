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
        if (preg_match('~www\.pixiv\.net/user/\d+/series/\d+~', $url, $matches)) {
            $res = $this->client->get($url);
            if ($res->getStatusCode() === 200) {
                $metadata = $this->ogpResolver->parse($res->getBody());
                $metadata->image = $this->proxize($metadata->image);

                return $metadata;
            } else {
                throw new \RuntimeException("{$res->getStatusCode()}: $url");
            }
        }

        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $illustId = $params['illust_id'];
        $page = 0;

        // 漫画ページ（ページ数はmanga_bigならあるかも）
        if ($params['mode'] === 'manga_big' || $params['mode'] === 'manga') {
            $page = $params['page'] ?? 0;
        }

        $res = $this->client->get('https://www.pixiv.net/ajax/illust/' . $illustId);
        if ($res->getStatusCode() === 200) {
            $json = json_decode($res->getBody()->getContents(), true);
            $metadata = new Metadata();

            $metadata->title = $json['body']['illustTitle'] ?? '';
            $metadata->description = '投稿者: ' . $json['body']['userName'] . PHP_EOL . strip_tags(str_replace('<br />', PHP_EOL, $json['body']['illustComment'] ?? ''));
            $metadata->image = $this->proxize($json['body']['urls']['original'] ?? '');

            // ページ数の指定がある場合は画像URLをそのページにする
            if ($page != 0) {
                $metadata->image = str_replace('_p0', '_p'.$page, $metadata->image);
            }

            // タグ
            if (!empty($json['body']['tags']['tags'])) {
                foreach ($json['body']['tags']['tags'] as $tag) {
                    // 一部の固定キーワードは無視
                    if (array_search($tag['tag'], ['R-18', 'イラスト', 'pixiv', 'ピクシブ'], true) === false) {
                        $metadata->tags[] = preg_replace('/\s/', '_', $tag['tag']);
                    }
                }
            }

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
