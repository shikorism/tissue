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
        @$dom->loadHTML(Formatter::htmlEntities($html, 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $tagsNode = $xpath->query("//div[@class='main_genre']/a");
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

        $res = $this->client->get($url);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($res->getBody(), 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // OGPタイトルから[]に囲まれているmakerを取得する
        // 複数の作者がいる場合スペース区切りになるためexplodeしている
        // スペースを含むmakerの場合名前の一部しか取れないが動作には問題ない
        preg_match('~ \[([^\[\]]*)\] (予告作品 )?\| DLsite~', $metadata->title, $match);
        $makers = explode(' ', $match[1]);

        //フォローボタン(.add_follow)はテキストを含んでしまうことがあるので要素を削除しておく
        $followButtonNode = $xpath->query('//*[@class="add_follow"]')->item(0);
        if ($followButtonNode !== null) {
            $followButtonNode->parentNode->removeChild($followButtonNode);
        }

        // maker, makerHeadを探す

        // makers
        // #work_makerから「makerを含むテキスト」を持つ要素を持つtdを探す
        // 作者名単体の場合もあるし、"作者A / 作者B"のようになることもある
        $makersNode = $xpath->query('//*[@id="work_maker"]//*[contains(text(), "' . $makers[0] . '")]/ancestor::td')->item(0);
        $makersArray = [];
        foreach ($makersNode->childNodes as $makerNode) {
            // 何らかのタグ(a, span)の場合のみ処理
            if ($makerNode->nodeType === XML_ELEMENT_NODE) {
                $makersArray[] = trim($makerNode->textContent);
            }
        }
        $makersArray = array_filter($makersArray);
        $makers = implode(' / ', $makersArray);

        // makersHaed
        // $makerNode(td)に対するthを探す
        // "著者", "サークル名", "ブランド名"など
        $makersHeadNode = $xpath->query('preceding-sibling::th', $makersNode)->item(0);
        $makersHead = trim($makersHeadNode->textContent);

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
