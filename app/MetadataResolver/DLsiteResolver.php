<?php

namespace App\MetadataResolver;

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

        $genreNode = $xpath->query("//div[@class='main_genre'][1]");
        if ($genreNode->length === 0) {
            return [];
        }

        $tagsNode = $genreNode->item(0)->getElementsByTagName('a');
        $tags = [];

        for ($i = 0; $i <= $tagsNode->length - 1; $i++) {
            $tags[] = $tagsNode->item($i)->textContent;
        }

        // 重複削除
        $tags = array_values(array_unique($tags));

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

        $res = $this->client->get($url);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // OGPタイトルから[]に囲まれているmakerを取得する
        // 複数の作者がいる場合スペース区切りになるためexplodeしている
        // スペースを含むmakerの場合名前の一部しか取れないが動作には問題ない
        preg_match('~ \[([^\[\]]*)\] (予告作品 )?\| DLsite(がるまに)?$~', $metadata->title, $match);
        $makers = explode(' ', $match[1]);

        //フォローボタン(.add_follow)はテキストを含んでしまうことがあるので要素を削除しておく
        $followButtonNode = $xpath->query('//*[@class="add_follow"]')->item(0);
        $followButtonNode->parentNode->removeChild($followButtonNode);

        // maker, makerHeadを探す

        // makers
        // #work_makerから「makerを含むテキスト」を持つ要素を持つtdを探す
        // 作者名単体の場合もあるし、"作者A / 作者B"のようになることもある
        $makersNode = $xpath->query('//*[@id="work_maker"]//*[contains(text(), "' . $makers[0] . '")]/ancestor::td')->item(0);
        // nbspをspaceに置換
        $makers = trim(str_replace("\xc2\xa0", ' ', $makersNode->textContent));

        // makersHaed
        // $makerNode(td)に対するthを探す
        // "著者", "サークル名", "ブランド名"など
        $makersHeadNode = $xpath->query('preceding-sibling::th', $makersNode)->item(0);
        $makersHead = trim($makersHeadNode->textContent);

        // 余分な文を消す

        // OGPタイトルから作者名とサイト名を消す
        $metadata->title = trim(preg_replace('~ \[[^\[\]]*\] (予告作品 )?\| DLsite(がるまに)?$~', '', $metadata->title));

        // OGP説明文から定型文を消す
        if (strpos($url, 'dlsite.com/eng/') || strpos($url, 'dlsite.com/ecchi-eng/')) {
            $metadata->description = preg_replace('~DLsite.+ is a download shop for .+With a huge selection of products, we\'re sure you\'ll find whatever tickles your fancy\. DLsite is one of the greatest indie contents download shops in Japan\.$~', '', $metadata->description);
        } else {
            $metadata->description = preg_replace('~「DLsite.+」は.+のダウンロードショップ。お気に入りの作品をすぐダウンロードできてすぐ楽しめる！毎日更新しているのであなたが探している作品にきっと出会えます。国内最大級の二次元総合ダウンロードショップ「DLsite」！$~', '', $metadata->description);
        }
        $metadata->description = trim(strip_tags($metadata->description));

        // 整形
        $metadata->description = $makersHead . ': ' . $makers . PHP_EOL . $metadata->description;
        $metadata->image = str_replace('img_sam.jpg', 'img_main.jpg', $metadata->image);
        $metadata->tags = $this->extractTags($res->getBody());

        return $metadata;
    }
}
