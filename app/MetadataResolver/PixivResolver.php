<?php

namespace App\MetadataResolver;

use Illuminate\Support\Facades\Log;

class PixivResolver implements Resolver
{
    public function thumbnail_to_master_url(string $url):string {
        // 最大長辺 1200 の画像に変換
        $url = str_replace("/c/128x128", "", $url);
        $url = str_replace("square1200.jpg", "master1200.jpg", $url);
        return $url;
    }

    public function proxize(string $url):string {
        // pixiv.cat のプロキシ URL に変換する
        $url = str_replace("i.pximg.net", "i.pixiv.cat", $url);
        return $url;
    }

    public function resolve(string $url): Metadata
    {
        preg_match("~illust_id=(\d+)~", parse_url($url)["query"], $illust_id);
        $illust_id = $illust_id[1];

        // 漫画ページかつページ数あり
        if (strpos(parse_url($url)["query"], "mode=manga_big") && strpos(parse_url($url)["query"], "page=")) {
            preg_match("~page=(\d+)~", parse_url($url)["query"], $match);
            $page = $match[1];

            // 未ログインでは漫画ページを開けないため、URL を作品ページに変換する
            Log::debug($url);
            $url = str_replace("mode=manga_big", "mode=medium", $url);
            Log::debug($url);

            $client = new \GuzzleHttp\Client();
            $res = $client->get($url);
            if ($res->getStatusCode() === 200) {
                $ogpResolver = new OGPResolver();
                $metadata = $ogpResolver->parse($res->getBody());

                preg_match("~https://i\.pximg\.net/c/128x128/img-master/img/\d{4}/\d{2}/\d{2}/\d{2}/\d{2}/\d{2}/{$illust_id}_p0_square1200\.jpg~", $res->getBody(), $match);
                $illust_thumbnail_url = $match[0];
                Log::debug($illust_thumbnail_url);

                $illust_url = $this->thumbnail_to_master_url($illust_thumbnail_url);
                Log::debug($illust_url);

                // 指定ページに変換
                $illust_url = str_replace("p0_master", "p{$page}_master", $illust_url);
                Log::debug($illust_url);

                $metadata->image =  $this->proxize($illust_url);;

                return $metadata;
            } else {
                throw new \RuntimeException("{$res->getStatusCode()}: $url");
            }
        }else {
            $client = new \GuzzleHttp\Client();
            $res = $client->get($url);
            if ($res->getStatusCode() === 200) {
                $ogpResolver = new OGPResolver();
                $metadata = $ogpResolver->parse($res->getBody());

                // OGP がデフォルト画像であるようならなんとかして画像を取得する
                if (strpos($metadata->image, "pixiv_logo.gif") || strpos($metadata->image, "pictures.jpg")) {

                    // 作品ページの場合のみ対応
                    if(strpos(parse_url($url)["query"], "mode=medium")){
                        preg_match("~https://i\.pximg\.net/c/128x128/img-master/img/\d{4}/\d{2}/\d{2}/\d{2}/\d{2}/\d{2}/{$illust_id}(_p0)?_square1200\.jpg~", $res->getBody(), $match);
                        $illust_thumbnail_url = $match[0];

                        $illust_url = $this->thumbnail_to_master_url($illust_thumbnail_url);

                        $metadata->image =  $this->proxize($illust_url);;
                    }
                }

                return $metadata;
            } else {
                throw new \RuntimeException("{$res->getStatusCode()}: $url");
            }

        }
    }
}
