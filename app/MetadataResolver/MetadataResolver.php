<?php

namespace App\MetadataResolver;

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
        'text/html' => OGPResolver::class
    ];

    public function resolve(string $url): Metadata
    {
        foreach ($this->rules as $pattern => $class) {
            if (preg_match($pattern, $url) === 1) {
                $resolver = new $class();

                return $resolver->resolve($url);
            }
        }

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
            } else {
                throw new \UnexpectedValueException('URL not matched.');
            }
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
