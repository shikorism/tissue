<?php

namespace App\MetadataResolver;

use App\Facades\Formatter;
use GuzzleHttp\Client;

class ComipoResolver implements Resolver
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
        $res = $this->client->get($url);
        $metadata = $this->ogpResolver->parse($res->getBody());

        $dom = new \DOMDocument();
        @$dom->loadHTML(Formatter::htmlEntities($res->getBody(), 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        // maker, makerHeadを探す
        // 作品情報は dt にmakerHead ("著者", "出版社"など)、dd にmakerが入っている
        // 出版社よりも著者を優先して表示する
        $makersHeadNode = $this->findProductInfoKey($xpath, '著者') ?? $this->findProductInfoKey($xpath, '出版社');
        $makersHead = trim($makersHeadNode->textContent);

        // makers
        // 作者名単体の場合もあるし、"作者A / 作者B"のようになることもある
        $makersArray = [];
        foreach ($xpath->query('following-sibling::dd[1]//a', $makersHeadNode) as $makerNode) {
            $makersArray[] = trim($makerNode->textContent);
        }
        $makersArray = array_filter($makersArray);
        $makers = implode(' / ', $makersArray);

        // OGPタイトルからサイト名を消す
        $metadata->title = trim(preg_replace('~ \| comipo$~', '', $metadata->title));

        // OGP説明文はサイトの定型文しか入っていないので、ページ内の作品紹介文を使う
        // 関連作品の紹介文も同じclassで後ろに出てくるので、最初のものを使う
        $introNode = $xpath->query('//div[@class="intro"]')->item(0);
        $description = $introNode !== null ? trim($introNode->textContent) : '';

        // 整形
        $metadata->description = $makersHead . ': ' . $makers . PHP_EOL . $description;

        $tags = [];
        $genreKeyNode = $this->findProductInfoKey($xpath, 'ジャンル');
        if ($genreKeyNode !== null) {
            foreach ($xpath->query('following-sibling::dd[1]//a', $genreKeyNode) as $node) {
                $tags[] = trim($node->textContent);
            }
        }
        // 重複削除
        $tags = array_values(array_unique($tags));
        sort($tags);
        $metadata->tags = $tags;

        return $metadata;
    }

    private function findProductInfoKey(\DOMXPath $xpath, string $key): ?\DOMNode
    {
        return $xpath->query('//dt[contains(@class, "c-productInfo__key") and normalize-space(text())="' . $key . '"]')->item(0);
    }
}
