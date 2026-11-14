<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Client;

class DLsiteResolver implements Resolver
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var OGPResolver
     */
    private $ogpResolver;
    /**
     * @var ComipoResolver
     */
    private $comipoResolver;

    public function __construct(Client $client, OGPResolver $ogpResolver)
    {
        $this->client = $client;
        $this->ogpResolver = $ogpResolver;
        $this->comipoResolver = new ComipoResolver($client, $ogpResolver);
    }

    /**
     * HTMLからタグとして利用可能な情報を抽出する
     * @param string $html ページ HTML
     * @return string[] タグ
     */
    public function extractTags(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($html, 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $tagsNode = $xpath->query('//dt[contains(@class, "c-productInfo__key") and normalize-space(text())="ジャンル"]/following-sibling::dd[1]//a');
        $tags = [];

        foreach ($tagsNode as $node) {
            $tags[] = $node->textContent;
        }

        // 重複削除
        $tags = array_values(array_unique($tags));
        sort($tags);

        return $tags;
    }

    public function resolve(string $url): Metadata
    {
        //アフィリエイトの場合は普通のURLに変換
        // ID型
        if (preg_match('~/dlaf/=(/.+/.+)?/link/~', $url)) {
            preg_match('~www\.dlsite\.com/(?P<genre>.+)/dlaf/=(/.+/.+)?/link/work/aid/(?P<AffiliateId>.+)/id/(?P<titleId>..\d+)(\.html)?~', $url, $matches);
            $url = "https://www.dlsite.com/{$matches['genre']}/work/=/product_id/{$matches['titleId']}.html";
        }
        // URL型
        if (strpos($url, '/dlaf/=/aid/') !== false) {
            preg_match('~www\.dlsite\.com/.+/dlaf/=/aid/.+/url/(?P<url>.+)~', $url, $matches);
            $affiliateUrl = urldecode($matches['url']);
            if (preg_match('~www\.dlsite\.com/.+/(work|announce)/=/product_id/..\d+(\.html)?~', $affiliateUrl, $matches)) {
                $url = $affiliateUrl;
            } else {
                throw new \RuntimeException("アフィリエイト先のリンクがDLsiteのタイトルではありません: $affiliateUrl");
            }
        }

        //スマホページの場合はPCページに正規化
        if (strpos($url, '-touch') !== false) {
            $url = str_replace('-touch', '', $url);
        }

        // DLsite comipoはcomipo.appに移転したので、旧URLの場合は新URLに書き換えてComipoResolverで処理する
        if (preg_match('~www\.dlsite\.com/comic/(work|announce)/=/product_id/(?P<titleId>..\d+)(\.html)?~', $url, $matches)) {
            return $this->comipoResolver->resolve("https://www.comipo.app/product/{$matches['titleId']}");
        }

        $res = $this->client->get($url);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($res->getBody(), 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // maker, makerHeadを探す
        // 作品情報は dl 内の dt にmakerHead ("著者", "サークル名", "ブランド名", "出版社名"など)、dd にmakerが入っている
        // 書籍の場合は出版社よりも著者を優先して表示する
        $makersHeadNode = $xpath->query('//dt[contains(@class, "c-productInfo__key") and normalize-space(text())="著者"]')->item(0);
        if ($makersHeadNode !== null) {
            $makersNodes = $xpath->query('following-sibling::dd[1]//a', $makersHeadNode);
        } else {
            $makerItemNode = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " c-productInfo__item--maker ")]')->item(0);
            $makersHeadNode = $xpath->query('.//dt', $makerItemNode)->item(0);
            $makersNodes = $xpath->query('.//dd//*[contains(@class, "c-productInfo__makerName")]', $makerItemNode);
        }
        $makersHead = trim($makersHeadNode->textContent);

        // makers
        // 作者名単体の場合もあるし、"作者A / 作者B"のようになることもある
        $makersArray = [];
        foreach ($makersNodes as $makerNode) {
            $makersArray[] = trim($makerNode->textContent);
        }
        $makersArray = array_filter($makersArray);
        $makers = implode(' / ', $makersArray);

        // 余分な文を消す

        // OGPタイトルから作者名とサイト名を消す
        $metadata->title = trim(preg_replace('~ \[[^\[\]]*\] (予告作品 )?\| DLsite(がるまに| comipo)?$~', '', $metadata->title));

        // OGP説明文から定型文を消す
        $metadata->description = preg_replace('~「DLsite( (同人|comipo|PCソフト|美少女ゲーム|成年コミック|がるまに))?( - R18)?」は.+のダウンロードショップ。お気に入りの作品をすぐダウンロードできてすぐ楽しめる！毎日更新しているのであなたが探している作品にきっと出会えます。国内最大級の二次元総合ダウンロードショップ「DLsite( comipo)?」！$~', '', $metadata->description);
        $metadata->description = trim(strip_tags($metadata->description));

        // 整形
        $metadata->description = $makersHead . ': ' . $makers . PHP_EOL . $metadata->description;
        $metadata->image = str_replace('img_sam.jpg', 'img_main.jpg', $metadata->image);
        $metadata->tags = $this->extractTags($res->getBody());

        return $metadata;
    }
}
