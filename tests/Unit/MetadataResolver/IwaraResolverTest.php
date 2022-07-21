<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\IwaraResolver;
use Tests\TestCase;

class IwaraResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testVideo()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/video.html');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://ecchi.iwara.tv/videos/5gd9auxxqkhw3w58o';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('鹿島とプリンツでLamb【ep5】', $metadata->title);
        $this->assertEquals('投稿者: nana77 shinshi' . PHP_EOL . 'コメント、like等ありがとうございます。' . PHP_EOL . '対決のルールは不明です。', $metadata->description);
        $this->assertEquals(['KanColle', 'nana77 shinshi'], $metadata->tags);
        $this->assertEquals('https://i.iwara.tv/sites/default/files/videos/thumbnails/901156/thumbnail-901156_0004.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame($url, (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testYouTube()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/youtube.html');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://iwara.tv/videos/z4dn6fag4iko08o0';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('むちむち天龍ちゃんで君色に染まる', $metadata->title);
        $this->assertEquals('投稿者: kochira' . PHP_EOL . 'Ray-cast test. Still trying to figure out how Ray-cast works so I\'m sorry if anything looks off.' . PHP_EOL . 'Unauthorized reproduction prohibited (無断転載は禁止です／未經授權禁止複製)', $metadata->description);
        $this->assertEquals(['KanColle', 'kochira'], $metadata->tags);
        $this->assertEquals('https://img.youtube.com/vi/pvA5Db082yo/maxresdefault.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame($url, (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testImages()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/images.html');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://iwara.tv/images/%E9%8F%A1%E9%9F%B3%E3%82%8A%E3%82%9318%E6%AD%B3';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('鏡音りん18歳', $metadata->title);
        $this->assertEquals('投稿者: Lion MUSASHI' . PHP_EOL . '今回はあんまエロくないです。', $metadata->description);
        $this->assertEquals(['Vocaloid', 'Lion MUSASHI'], $metadata->tags);
        $this->assertEquals('https://i.iwara.tv/sites/default/files/photos/jing_yin_rin18sui_a.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame($url, (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
