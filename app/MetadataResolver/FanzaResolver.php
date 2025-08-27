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

        // 動画旧URLの変換
        if (preg_match('~www\.dmm\.co\.jp/digital/(videoa|videoc|anime)/-/detail/=/cid=([0-9a-z_]+)~', $url, $matches)) {
            $categories = ['videoa' => 'av', 'videoc' => 'amateur'];
            $url = 'https://video.dmm.co.jp/' . ($categories[$matches[1]] ?? $matches[1]) . '/content?id=' . $matches[2];
        }

        // 動画
        if (preg_match('~video\.dmm\.co\.jp/(av|amateur|anime)/content~', $url, $matches)) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            $query = <<<'GRAPHQL'
            query ContentPageData($id: ID!, $isAmateur: Boolean!, $isAnime: Boolean!, $isAv: Boolean!, $isCinema: Boolean!) {
              ppvContent(id: $id) {
                ...ContentData
                __typename
              }
            }
            fragment ContentData on PPVContent {
              id
              floor
              title
              isExclusiveDelivery
              releaseStatus
              description
              notices
              isNoIndex
              isAllowForeign
              announcements {
                body
                __typename
              }
              featureArticles {
                link {
                  url
                  text
                  __typename
                }
                __typename
              }
              packageImage {
                largeUrl
                mediumUrl
                __typename
              }
              sampleImages {
                number
                imageUrl
                largeImageUrl
                __typename
              }
              products {
                ...ProductData
                __typename
              }
              mostPopularContentImage {
                ... on ContentSampleImage {
                  __typename
                  largeImageUrl
                  imageUrl
                }
                ... on PackageImage {
                  __typename
                  largeUrl
                  mediumUrl
                }
                __typename
              }
              priceSummary {
                lowestSalePrice
                lowestPrice
                campaign {
                  title
                  id
                  endAt
                  __typename
                }
                __typename
              }
              weeklyRanking: ranking(term: Weekly)
              monthlyRanking: ranking(term: Monthly)
              wishlistCount
              sample2DMovie {
                fileID
                highestMovieUrl
                __typename
              }
              sampleVRMovie {
                highestMovieUrl
                __typename
              }
              ...AmateurAdditionalContentData @include(if: $isAmateur)
              ...AnimeAdditionalContentData @include(if: $isAnime)
              ...AvAdditionalContentData @include(if: $isAv)
              ...CinemaAdditionalContentData @include(if: $isCinema)
              __typename
            }
            fragment ProductData on PPVProduct {
              id
              priority
              deliveryUnit {
                id
                priority
                streamMaxQualityGroup
                downloadMaxQualityGroup
                __typename
              }
              priceInclusiveTax
              sale {
                priceInclusiveTax
                __typename
              }
              expireDays
              licenseType
              shopName
              availableCoupon {
                name
                expirationPolicy {
                  ... on ProductCouponExpirationAt {
                    expirationAt
                    __typename
                  }
                  ... on ProductCouponExpirationDay {
                    expirationDays
                    __typename
                  }
                  __typename
                }
                expirationAt
                discountedPrice
                minPayment
                destinationUrl
                __typename
              }
              __typename
            }
            fragment AmateurAdditionalContentData on PPVContent {
              deliveryStartDate
              duration
              amateurActress {
                id
                name
                imageUrl
                age
                waist
                bust
                bustCup
                height
                hip
                relatedContents {
                  id
                  title
                  __typename
                }
                __typename
              }
              maker {
                id
                name
                __typename
              }
              label {
                id
                name
                __typename
              }
              genres {
                id
                name
                __typename
              }
              makerContentId
              playableInfo {
                ...PlayableInfo
                __typename
              }
              __typename
            }
            fragment PlayableInfo on PlayableInfo {
              playableDevices {
                deviceDeliveryUnits {
                  id
                  deviceDeliveryQualities {
                    isDownloadable
                    isStreamable
                    __typename
                  }
                  __typename
                }
                device
                name
                priority
                __typename
              }
              deviceGroups {
                id
                devices {
                  deviceDeliveryUnits {
                    deviceDeliveryQualities {
                      isStreamable
                      isDownloadable
                      __typename
                    }
                    __typename
                  }
                  __typename
                }
                __typename
              }
              vrViewingType
              __typename
            }
            fragment AnimeAdditionalContentData on PPVContent {
              deliveryStartDate
              duration
              series {
                id
                name
                __typename
              }
              maker {
                id
                name
                __typename
              }
              label {
                id
                name
                __typename
              }
              genres {
                id
                name
                __typename
              }
              makerContentId
              playableInfo {
                ...PlayableInfo
                __typename
              }
              __typename
            }
            fragment AvAdditionalContentData on PPVContent {
              deliveryStartDate
              makerReleasedAt
              duration
              actresses {
                id
                name
                nameRuby
                imageUrl
                __typename
              }
              histrions {
                id
                name
                __typename
              }
              directors {
                id
                name
                __typename
              }
              series {
                id
                name
                __typename
              }
              maker {
                id
                name
                __typename
              }
              label {
                id
                name
                __typename
              }
              genres {
                id
                name
                __typename
              }
              contentType
              relatedWords
              makerContentId
              playableInfo {
                ...PlayableInfo
                __typename
              }
              __typename
            }
            fragment CinemaAdditionalContentData on PPVContent {
              deliveryStartDate
              duration
              actresses {
                id
                name
                nameRuby
                imageUrl
                __typename
              }
              histrions {
                id
                name
                __typename
              }
              directors {
                id
                name
                __typename
              }
              authors {
                id
                name
                __typename
              }
              series {
                id
                name
                __typename
              }
              maker {
                id
                name
                __typename
              }
              label {
                id
                name
                __typename
              }
              genres {
                id
                name
                __typename
              }
              makerContentId
              playableInfo {
                ...PlayableInfo
                __typename
              }
              __typename
            }
            GRAPHQL;
            $variables = [
                'id' => $params['id'],
                'isAv' => $matches[1] === 'av',
                'isAmateur' => $matches[1] === 'amateur',
                'isAnime' => $matches[1] === 'anime',
                'isCinema' => false,
            ];

            $queryRes = $this->client->post('https://api.video.dmm.co.jp/graphql', [
                'cookies' => $cookieJar,
                'headers' => [
                    'Accept' => 'application/graphql-response+json, application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'operationName' => 'ContentPageData',
                    'query' => $query,
                    'variables' => $variables,
                ]
            ]);
            $json = json_decode($queryRes->getBody()->getContents(), true);
            $ppvContent = $json['data']['ppvContent'];

            $metadata = new Metadata();
            $metadata->title = $ppvContent['title'];
            $metadata->description = trim(strip_tags(str_replace('<br>', "\n", $ppvContent['description'])));
            $metadata->image = $ppvContent['packageImage']['largeUrl'] ?? $ppvContent['packageImage']['mediumUrl'];

            $tags = [];
            foreach (($ppvContent['actresses'] ?? []) as $actress) {
                $tags[] = $actress['name'];
            }
            if (!empty($ppvContent['amateurActress'])) {
                $tags[] = $ppvContent['amateurActress']['name'];
            }
            if (!empty($ppvContent['series'])) {
                $tags[] = $ppvContent['series']['name'];
            }
            if (!empty($ppvContent['maker'])) {
                $tags[] = $ppvContent['maker']['name'];
            }
            if (!empty($ppvContent['label'])) {
                $tags[] = $ppvContent['label']['name'];
            }
            foreach ($ppvContent['genres'] as $genre) {
                $tags[] = $genre['name'];
            }
            $metadata->tags = $this->array_finish($tags);

            return $metadata;
        }

        $res = $this->client->get($url, ['cookies' => $cookieJar]);
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);

        // 同人
        if (mb_strpos($url, 'www.dmm.co.jp/dc/doujin/-/detail/') !== false) {
            $genre = $this->array_finish($crawler->filter('.m-productInformation .informationList a:not([href="#update-top"])')->extract(['_text']));
            $genre = array_filter($genre, (function ($text) {
                return !preg_match('~％OFF対象$~', $text);
            }));

            $metadata = new Metadata();
            $metadata->title = trim($crawler->filter('meta[property="og:title"]')->attr('content'));
            $metadata->description = trim($crawler->filter('.summary__txt')->text('', false));
            $metadata->image = $crawler->filter('meta[property="og:image"]')->attr('content');
            $metadata->tags = array_merge($genre, [$crawler->filter('.circleName__txt')->text('')]);

            return $metadata;
        }

        // 電子書籍
        if (mb_strpos($url, 'book.dmm.co.jp/product/') !== false || mb_strpos($url, 'book.dmm.co.jp/detail/') !== false) {
            $json = $crawler->filter('script[type="application/ld+json"]')->first()->text('', false);
            $data = json_decode($json, true);

            // DomCrawler内でjson内の日本語がHTMLエンティティに変換されるので、全要素に対してhtml_entity_decode
            array_walk_recursive($data, function (&$v) {
                $v = html_entity_decode($v);
            });

            $metadata = new Metadata();
            $metadata->title = $data['name'];
            $metadata->description = $data['description'];
            $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $crawler->filter('meta[property="og:image"]')->attr('content'));
            $metadata->tags = $this->array_finish([...$data['subjectOf']['author']['name'], $data['subjectOf']['publisher']['name'], ...$data['subjectOf']['genre']]);

            return $metadata;
        }

        // PCゲーム
        if (mb_strpos($url, 'dlsoft.dmm.co.jp/detail/') !== false) {
            $metadata = new Metadata();
            $metadata->title = trim($crawler->filter('.productTitle__headline')->text(''));
            $metadata->description = trim($crawler->filter('.area-detail-read .text-overflow')->text('', false));
            $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $crawler->filter('meta[property="og:image"]')->attr('content'));
            $metadata->tags = $this->array_finish($crawler->filter('.contentsDetailBottom a[href*="list/?"]')->extract(['_text']));

            return $metadata;
        }

        // 上で特に対応しなかったURL 画像の置換くらいはしておく
        $metadata = $this->ogpResolver->parse($html);
        $metadata->image = preg_replace("~(pr|ps)\.jpg$~", 'pl.jpg', $metadata->image);

        return $metadata;
    }
}
