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

    public function resolve(string $url): Metadata
    {

        //スマホページの場合はPCページに正規化
        if (strpos($url, '-touch') !== false) {
            $url = str_replace('-touch', '', $url);
        }

        $res = $this->client->get($url);
        if ($res->getStatusCode() === 200) {
            $metadata = $this->ogpResolver->parse($res->getBody());

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new \DOMXPath($dom);

            // OGPタイトルから[]に囲まれているmakerを取得する
            // 複数の作者がいる場合スペース区切りになるためexplodeしている
            // スペースを含むmakerの場合名前の一部しか取れないが動作には問題ない
            preg_match('~ \[([^\[\]]*)\] \| DLsite(がるまに)?$~', $metadata->title, $match);
            $makers = explode(' ', $match[1]);

            //フォローボタン(.btn_follow)はテキストを含んでしまうことがあるので要素を削除しておく
            $followButtonNode = $xpath->query('//*[@class="btn_follow"]')->item(0);
            $followButtonNode->parentNode->removeChild($followButtonNode);

            // maker, makerHeadを探す

            // makers
            // #work_makerから「makerを含むテキスト」を持つ要素を持つtdを探す
            // 作者名単体の場合もあるし、"作者A / 作者B"のようになることもある
            $makersNode = $xpath->query('//*[@id="work_maker"]//*[contains(text(), "' . $makers[0] . '")]/ancestor::td')->item(0);
            $makers = trim($makersNode->textContent);

            // makersHaed
            // $makerNode(td)に対するthを探す
            // "著者", "サークル名", "ブランド名"など
            $makersHeadNode = $xpath->query('preceding-sibling::th', $makersNode)->item(0);
            $makersHead = trim($makersHeadNode->textContent);

            // 余分な文を消す

            // OGPタイトルから作者名とサイト名を消す
            $metadata->title = trim(preg_replace('~ \[([^\[\]]*)\] \| DLsite(がるまに)?$~', '', $metadata->title));

            // OGP説明文から定型文を消す
            if (strpos($url, 'dlsite.com/eng/') || strpos($url, 'dlsite.com/ecchi-eng/')) {
                $metadata->description = trim(preg_replace('~DLsite.+ is a download shop for .+With a huge selection of products, we\'re sure you\'ll find whatever tickles your fancy\. DLsite is one of the greatest indie contents download shops in Japan\.$~', '', $metadata->description));
            } else {
                $metadata->description = trim(preg_replace('~「DLsite.+」は.+のダウンロードショップ。お気に入りの作品をすぐダウンロードできてすぐ楽しめる！毎日更新しているのであなたが探している作品にきっと出会えます。国内最大級の二次元総合ダウンロードショップ「DLsite」！$~', '', $metadata->description));
            }

            // 整形
            $metadata->description = $makersHead . ': ' . $makers . PHP_EOL . $metadata->description;
            $metadata->image = str_replace('img_sam.jpg', 'img_main.jpg', $metadata->image);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
