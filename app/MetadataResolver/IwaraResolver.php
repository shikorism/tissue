<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;

class IwaraResolver implements Resolver
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function resolve(string $url): Metadata
    {
        preg_match('~iwara\.tv/(?P<type>video|image)[s]?/(?P<id>\w+)~', $url, $m);

        if (!isset($m['type']) || !isset($m['id'])) {
            throw new \RuntimeException("Invalid URL: $url");
        }

        $metadata = new Metadata();

        $apiUrl = "https://api.iwara.tv/{$m['type']}/{$m['id']}";
        $res = $this->client->get($apiUrl);
        $json = json_decode($res->getBody(), true);

        $metadata->title = $json['title'];
        $metadata->description = "投稿者: {$json['user']['name']}" . PHP_EOL . trim(str_replace("\n\n", "\n", $json['body']));

        $excludeTags = ['uncategorized', 'other'];
        // excludeTagsのidを持つタグを除外し、配列のindexを0から再構成する
        $metadata->tags = array_values(array_diff(array_column($json['tags'], 'id'), $excludeTags));
        // 投稿者のusernameをタグに追加する
        // nameにはスペースなどの空白が含まれていることがあるためusernameを使用する
        $metadata->tags[] = $json['user']['username'];

        switch ($m['type']) {
            case 'video':
                if ($json['embedUrl']) {
                    // 埋め込みタイプの動画
                    // 過去にはvimeoに対応していたが現在ではYouTubeのみ対応している
                    switch (parse_url($json['embedUrl'], PHP_URL_HOST)) {
                        case 'www.youtube.com':
                            parse_str(parse_url($json['embedUrl'], PHP_URL_QUERY), $params);
                            $id = $params['v'];
                            $metadata->image = "https://img.youtube.com/vi/{$id}/maxresdefault.jpg";
                            break;
                        case 'youtu.be':
                            $id = basename($json['embedUrl']);
                            $metadata->image = "https://img.youtube.com/vi/{$id}/maxresdefault.jpg";
                            break;
                        default:
                            throw new \RuntimeException("Unsupported embed type: {$json['embedUrl']} in $url");
                    }
                } else {
                    // 通常のアップロードされた動画
                    $fileNumber = sprintf('%02d', $json['thumbnail']);
                    $thumbnailUrl = "https://files.iwara.tv/image/original/{$json['file']['id']}/thumbnail-{$fileNumber}.jpg";
                    $metadata->image = $thumbnailUrl;
                }
                break;

            case 'image':
                $fileName = str_replace('.png', '.jpg', $json['thumbnail']['name']);
                $metadata->image = "https://files.iwara.tv/image/large/{$json['thumbnail']['id']}/{$fileName}";
                break;

            default:
                throw new \RuntimeException("Unsupported type: {$m['type']} in $url");
        }

        return $metadata;
    }
}
