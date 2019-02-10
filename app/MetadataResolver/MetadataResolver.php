<?php

namespace App\MetadataResolver;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class MetadataResolver implements Resolver
{
    public $rules = [
        '~(((sp\.)?seiga\.nicovideo\.jp/seiga(/#!)?|nico\.ms))/im~' => NicoSeigaResolver::class,
        '~nijie\.info/view(_popup)?\.php~' => NijieResolver::class,
        '~komiflo\.com(/#!)?/comics/(\\d+)~' => KomifloResolver::class,
        '~www\.melonbooks\.co\.jp/detail/detail\.php~' => MelonbooksResolver::class,
        '~ec\.toranoana\.jp/tora_r/ec/item/.*~' => ToranoanaResolver::class,
        '~iwara\.tv/videos/.*~' => IwaraResolver::class,
        '~www\.dlsite\.com/.*/work/=/product_id/..\d+\.html~' => DLsiteResolver::class,
        '~www\.pixiv\.net/member_illust\.php\?illust_id=\d+~' => PixivResolver::class,
        '~fantia\.jp/posts/\d+~' => FantiaResolver::class,
        '~dmm\.co\.jp/~' => FanzaResolver::class,
        '~www\.patreon\.com/~' => PatreonResolver::class,
        '~www\.deviantart\.com/.*/art/.*~' => DeviantArtResolver::class,
        '~\.syosetu\.com/n\d+[a-z]{2,}~' => NarouResolver::class,
    ];

    public $mimeTypes = [
        'application/activity+json' => ActivityPubResolver::class,
        'application/ld+json' => ActivityPubResolver::class,
        'text/html' => OGPResolver::class,
        '*/*' => OGPResolver::class
    ];

    public $defaultResolver = OGPResolver::class;

    public function resolve(string $url): Metadata
    {
        foreach ($this->rules as $pattern => $class) {
            if (preg_match($pattern, $url) === 1) {
                $resolver = new $class();

                return $resolver->resolve($url);
            }
        }

        $result = $this->resolveWithAcceptHeader($url);
        if ($result !== null) {
            return $result;
        }

        if (isset($this->defaultResolver)) {
            $resolver = new $this->defaultResolver();
            return $resolver->resolve($url);
        }

        throw new \UnexpectedValueException('URL not matched.');
    }
    
    public function resolveWithAcceptHeader(string $url): ?Metadata
    {
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $url, [
                'headers' => [
                    'Accept' => implode(', ', array_keys($this->mimeTypes))
                ]
            ]);

            if ($res->getStatusCode() === 200) {
                preg_match('/^[^;\s]+/', $res->getHeaderLine('Content-Type'), $matches);
                $mimeType = $matches[0];

                if (isset($this->mimeTypes[$mimeType])) {
                    $class = $this->mimeTypes[$mimeType];
                    $parser = new $class();

                    return $parser->parse($res->getBody());
                }

                if (isset($this->mimeTypes['*/*'])) {
                    $class = $this->mimeTypes['*/*'];
                    $parser = new $class();

                    return $parser->parse($res->getBody());
                }
            } else {
                // code < 400 && code !== 200 => fallback
            }
        } catch (ClientException $e) {
            // 406 Not Acceptable は多分Acceptが原因なので無視してフォールバック
            if ($e->getResponse()->getStatusCode() !== 406) {
                throw $e;
            }
        } catch (ServerException $e) {
            // 5xx は変なAcceptが原因かもしれない（？）ので無視してフォールバック
        }

        return null;
    }
}
