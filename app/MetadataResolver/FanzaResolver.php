<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;

class FanzaResolver implements Resolver
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
     * arrayの各要素をtrim・スペースの_置換をした後、重複した値を削除してキーを詰め直す
     *
     * @param array $array
     *
     * @return array 処理されたarray
     */
    public function array_finish(array $array): array
    {
        $array = array_map('trim', $array);
        $array = array_map((function ($value) {
            return str_replace(' ', '_', $value);
        }), $array);
        $array = array_unique($array);
        $array = array_values($array);

        return $array;
    }

    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['age_check_done' => '1'], 'dmm.co.jp');

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);

        // 動画
        if (preg_match('~www\.dmm\.co\.jp/digital/(videoa|videoc|anime)/-/detail~', $url)) {
            $metadata = new Metadata();
            $metadata->title = trim($crawler->filter('#title')->text(''));
            $metadata->description = trim($crawler->filter('.box-rank+table+div+div')->text(''));
            $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $crawler->filter('meta[property="og:image"]')->attr('content'));
            $metadata->tags = $this->array_finish($crawler->filter('.box-rank+table a:not([href="#review"])')->extract(['_text']));

            return $metadata;
        }

        // 同人
        if (mb_strpos($url, 'www.dmm.co.jp/dc/doujin/-/detail/') !== false) {
            $genre = $this->array_finish($crawler->filter('.m-productInformation a:not([href="#update-top"])')->extract(['_text']));
            $genre = array_filter($genre, (function ($text) {
                return !preg_match('~％OFF対象$~', $text);
            }));

            $metadata = new Metadata();
            $metadata->title = $crawler->filter('meta[property="og:title"]')->attr('content');
            $metadata->description = trim($crawler->filter('.summary__txt')->text(''));
            $metadata->image = $crawler->filter('meta[property="og:image"]')->attr('content');
            $metadata->tags = array_merge($genre, [$crawler->filter('.circleName__txt')->text('')]);

            return $metadata;
        }

        // 電子書籍
        if (mb_strpos($url, 'book.dmm.co.jp/detail/') !== false) {
            $metadata = new Metadata();
            $metadata->title = trim($crawler->filter('#title')->text(''));
            $metadata->description = trim($crawler->filter('.m-boxDetailProduct__info__story')->text(''));
            $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $crawler->filter('meta[property="og:image"]')->attr('content'));
            $metadata->tags = $this->array_finish($crawler->filter('.m-boxDetailProductInfoMainList__description__list__item, .m-boxDetailProductInfo__list__description__item a')->extract(['_text']));

            return $metadata;
        }

        // PCゲーム
        if (mb_strpos($url, 'dlsoft.dmm.co.jp/detail/') !== false) {
            $metadata = new Metadata();
            $metadata->title = trim($crawler->filter('#title')->text(''));
            $metadata->description = trim($crawler->filter('.area-detail-read .text-overflow')->text(''));
            $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $crawler->filter('meta[property="og:image"]')->attr('content'));
            $metadata->tags = $this->array_finish($crawler->filter('.container02 table a[href*="list/article="]')->extract(['_text']));

            return $metadata;
        }

        // 上で特に対応しなかったURL 画像の置換くらいはしておく
        $metadata = $this->ogpResolver->parse($html);
        $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $metadata->image);

        return $metadata;
    }
}
