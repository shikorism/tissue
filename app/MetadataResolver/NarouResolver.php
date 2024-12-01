<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class NarouResolver implements Resolver
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
        $cookieJar = CookieJar::fromArray(['over18' => 'yes'], '.syosetu.com');


        preg_match('~\.syosetu\.com/(novelview/infotop/ncode/)?(?P<ncode>n\d+[a-z]+)~', $url, $matches);
        $ncode = $matches['ncode'];

        $res = $this->client->get("https://novel18.syosetu.com/novelview/infotop/ncode/$ncode/", ['cookies' => $cookieJar]);
        $html = $res->getBody()->getContents();

        // 一見旧式のDOMDocumentを使っているように見えるがこれは罠で、なろうのHTMLはDOMCrawlerだとパースに失敗する
        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($html, 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win'));
        $xpath = new \DOMXPath($dom);

        $metadata = $this->ogpResolver->parse($html);
        $description = [];

        // タイトル
        $titleNodeList = $xpath->query('//h1/a');
        if ($titleNodeList->length !== 0) {
            $metadata->title = $titleNodeList->item(0)->textContent;
        }

        // タグ
        $keywordNodeList = $xpath->query('//th[contains(text(), "キーワード")]/following-sibling::td[1]');
        if ($keywordNodeList->length !== 0) {
            $keyword = trim($keywordNodeList->item(0)->textContent);
            $metadata->tags = preg_split('/\s+/u', $keyword);
        }

        // 作者名
        $authorNodeList = $xpath->query('//a[contains(@href,"mypage.syosetu.com")]');
        if ($authorNodeList->length !== 0) {
            $description[] = '作者: ' . trim($authorNodeList->item(0)->textContent);
        }

        // あらすじがあれば先頭150文字を取得する
        $exNodeList = $xpath->query('//td[@class="ex"]');
        if ($exNodeList->length !== 0) {
            $summary = trim($exNodeList->item(0)->textContent);
            // 長過ぎたら150文字に切って「……」をつける
            if (mb_strlen($summary) >= 148) {
                $description[] = mb_substr($summary, 0, 150) . '……';
            } else {
                $description[] = $summary;
            }
        }

        // 作者名とあらすじをくっつける
        $metadata->description = implode("\n", $description);

        return $metadata;
    }
}
