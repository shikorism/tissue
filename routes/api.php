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

Route::get('/ogp', function (Request $request) {
    $request->validate([
        'url:required|url'
    ]);

    $client = new GuzzleHttp\Client();
    $res = $client->get($request->input('url'));
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

        $response = response()->json($result);
        if (!config('app.debug')) {
            $response = $response->setCache(['public' => true, 'max_age' => 86400]);
        }
        return $response;
    } else {
        abort($res->getStatusCode());
    }
});