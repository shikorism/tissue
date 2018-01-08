<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/checkin/card', function (Request $request) {
    $request->validate([
        'url:required|url'
    ]);
    $url = $request->input('url');

    $client = new GuzzleHttp\Client();
    $res = $client->get($url);
    if ($res->getStatusCode() === 200) {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);

        $result = [
            'title' => '',
            'description' => '',
            'image' => ''
        ];

        $titleNode = $xpath->query('//meta[@*="og:title"]');
        foreach ($titleNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $result['title'] = $node->getAttribute('content');
                break;
            }
        }

        $descriptionNode = $xpath->query('//meta[@*="og:description"]');
        foreach ($descriptionNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $result['description'] = $node->getAttribute('content');
                break;
            }
        }

        $imageNode = $xpath->query('//meta[@*="og:image"]');
        foreach ($imageNode as $node) {
            if (!empty($node->getAttribute('content'))) {
                $result['image'] = $node->getAttribute('content');
                break;
            }
        }

        // 一部サイトについては別のサムネイルの取得を試みる
        if (mb_strpos($url, 'nico.ms/im') !== false ||
            mb_strpos($url, 'seiga.nicovideo.jp/seiga/im') !== false ||
            mb_strpos($url, 'sp.seiga.nicovideo.jp/seiga/#!/im') !== false) {
            // ニコニコ静画用の処理
            preg_match('~http://(?:(?:sp\\.)?seiga\\.nicovideo\\.jp/seiga(?:/#!)?|nico\\.ms)/im(\\d+)~', $url, $matches);
            $result['image'] = "http://lohas.nicoseiga.jp/thumb/${matches[1]}l?";
        } elseif (mb_strpos($url, 'nijie.info/view.php')) {
            // ニジエ用の処理
            $dataNode = $xpath->query('//script[substring(@type, string-length(@type) - 3, 4) = "json"]');
            foreach ($dataNode as $node) {
                $imageData = json_decode($node->nodeValue, true);
                if (isset($imageData['thumbnailUrl'])) {
                    $result['image'] = preg_replace('~nijie\\.info/.*/nijie_picture/~', 'nijie.info/nijie_picture/', $imageData['thumbnailUrl']);
                    break;
                }
            }
        }

        $response = response()->json($result);
        if (!config('app.debug')) {
            $response = $response->setCache(['public' => true, 'max_age' => 86400]);
        }
        return $response;
    } else {
        abort($res->getStatusCode());
    }
});