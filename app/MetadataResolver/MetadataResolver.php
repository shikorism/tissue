<?php

namespace App\MetadataResolver;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class MetadataResolver implements Resolver
{
    public $rules = [
        '~(((sp\.)?seiga\.nicovideo\.jp/seiga(/#!)?|nico\.ms))/im~' => NicoSeigaResolver::class,
        '~nijie\.info/view(_popup)?\.php~' => NijieResolver::class,
        '~komiflo\.com(/#!)?/comics/(\\d+)~' => KomifloResolver::class,
        '~www\.melonbooks\.co\.jp/detail/detail\.php~' => MelonbooksResolver::class,
        '~ec\.toranoana\.(jp|shop)/(tora|joshi)(_[rd]+)?/(ec|digi)/item/~' => ToranoanaResolver::class,
        '~((www|ecchi)\.)?iwara\.tv/(video|image)[s]?/\w+~' => IwaraResolver::class,
        '~www\.dlsite\.com/.*/(work|announce)/=/product_id/..\d+(\.html)?~' => DLsiteResolver::class,
        '~www\.dlsite\.com/.*/dlaf/=(/.+/.+)?/link/work/aid/.+(/id)?/..\d+(\.html)?~' => DLsiteResolver::class,
        '~www\.dlsite\.com/.*/dlaf/=/aid/.+/url/.+~' => DLsiteResolver::class,
        '~dlsite\.jp/...tw/..\d+~' => DLsiteResolver::class,
        '~www\.pixiv\.net/member_illust\.php\?illust_id=\d+~' => PixivResolver::class,
        '~www\.pixiv\.net/(en/)?artworks/\d+~' => PixivResolver::class,
        '~www\.pixiv\.net/user/\d+/series/\d+~' => PixivResolver::class,
        '~www\.pixiv\.net/novel/show\.php\?id=\d+~' => PixivNovelResolver::class,
        '~fantia\.jp/posts/\d+~' => FantiaResolver::class,
        '~dmm\.co\.jp/~' => FanzaResolver::class,
        '~www\.patreon\.com/~' => PatreonResolver::class,
        '~www\.deviantart\.com/.*/art/.*~' => DeviantArtResolver::class,
        '~\.syosetu\.com/(novelview/infotop/ncode/)?n\d+[a-z]+~' => NarouResolver::class,
        '~ci-en\.(jp|net|dlsite\.com)/creator/\d+/article/\d+~' => CienResolver::class,
        '~www\.plurk\.com\/p\/.*~' => PlurkResolver::class,
        '~store\.steampowered\.com/app/\d+~' => SteamResolver::class,
        '~ss\.kb10uy\.org/posts/\d+$~' => Kb10uyShortStoryServerResolver::class,
        '~www\.hentai-foundry\.com/pictures/user/.+/\d+/.+~' => HentaiFoundryResolver::class,
        '~(www\.)?((mobile|m)\.)?(twitter|x)\.com/(#!/)?[0-9a-zA-Z_]{1,15}/status(es)?/([0-9]+)(/photo/[1-4])?/?(\\?.+)?$~' => TwitterResolver::class,
        '~www\.mgstage\.com/~' => MGStageResolver::class,
        '~booth.pm/([a-z]+/)?items/[0-9]+~' => BoothResolver::class,
    ];

    public $mimeTypes = [
        'application/activity+json' => ActivityPubResolver::class,
        'application/ld+json' => ActivityPubResolver::class,
        'text/html' => OGPResolver::class,
        '*/*' => OGPResolver::class
    ];

    public $defaultResolver = OGPResolver::class;

    public function __construct(private Client $client)
    {
    }

    public function resolve(string $url): Metadata
    {
        foreach ($this->rules as $pattern => $class) {
            if (preg_match($pattern, $url) === 1) {
                try {
                    /** @var Resolver $resolver */
                    $resolver = app($class, ['client' => $this->client]);

                    return $resolver->resolve($url);
                } catch (UnsupportedContentException $e) {
                }
            }
        }

        try {
            return $this->resolveWithAcceptHeader($url);
        } catch (UnsupportedContentException $e) {
        }

        if (isset($this->defaultResolver)) {
            /** @var Resolver $resolver */
            $resolver = app($this->defaultResolver, ['client' => $this->client]);

            return $resolver->resolve($url);
        }

        throw new \UnexpectedValueException('URL not matched.');
    }

    public function resolveWithAcceptHeader(string $url): Metadata
    {
        try {
            // Rails等はAcceptに */* が入っていると、ブラウザの適当なAcceptヘッダだと判断して全部無視してしまう。
            // c.f. https://github.com/rails/rails/issues/9940
            // そこでここでは */* を「Acceptヘッダを無視してきたレスポンス（よくある）」のハンドラとして扱い、
            // Acceptヘッダには */* を足さないことにする。
            $acceptTypes = array_diff(array_keys($this->mimeTypes), ['*/*']);

            $res = $this->client->request('GET', $url, [
                'headers' => [
                    'Accept' => implode(', ', $acceptTypes)
                ]
            ]);

            if ($res->getStatusCode() === 200) {
                preg_match('/^[^;\s]+/', $res->getHeaderLine('Content-Type'), $matches);
                $mimeType = $matches[0];

                if (isset($this->mimeTypes[$mimeType])) {
                    $class = $this->mimeTypes[$mimeType];
                    $parser = app($class, ['client' => $this->client]);

                    return $parser->parse($res->getBody());
                }

                if (isset($this->mimeTypes['*/*'])) {
                    $class = $this->mimeTypes['*/*'];
                    $parser = app($class, ['client' => $this->client]);

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

        throw new UnsupportedContentException();
    }
}
