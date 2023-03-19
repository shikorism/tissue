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

    public function testOldVideoUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/video.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://ecchi.iwara.tv/videos/5gd9auxxqkhw3w58o';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('鹿島とプリンツでLamb【ep5】', $metadata->title);
        $this->assertEquals('投稿者: nana77 shinshi' . PHP_EOL . 'コメント、like等ありがとうございます。' . PHP_EOL . '対決のルールは不明です。', $metadata->description);
        $this->assertEquals(['nana77shinshi'], $metadata->tags);
        $this->assertEquals('https://files.iwara.tv/image/original/f8aaafb8-3c5e-454e-aa14-423a06ce220b/thumbnail-00.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/video/5gd9auxxqkhw3w58o', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testOldVideoUrlYouTube()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/youtube.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://iwara.tv/videos/z4dn6fag4iko08o0';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('むちむち天龍ちゃんで君色に染まる', $metadata->title);
        $this->assertEquals('投稿者: kochira' . PHP_EOL . 'Ray-cast test. Still trying to figure out how Ray-cast works so I\'m sorry if anything looks off.' . PHP_EOL . 'Unauthorized reproduction prohibited (無断転載は禁止です／未經授權禁止複製)', $metadata->description);
        $this->assertEquals(['kochira'], $metadata->tags);
        $this->assertEquals('https://img.youtube.com/vi/pvA5Db082yo/maxresdefault.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/video/z4dn6fag4iko08o0', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testOldImageUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/image.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://www.iwara.tv/image/Mpf9JAonH9rTjG/';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('鏡音りん18歳', $metadata->title);
        $this->assertEquals('投稿者: Lion MUSASHI' . PHP_EOL . '今回はあんまエロくないです。', $metadata->description);
        $this->assertEquals(['lionmusashi'], $metadata->tags);
        $this->assertEquals('https://files.iwara.tv/image/large/bb970f8e-3dec-47b5-a8cf-b85abb32f13a/jing_yin_rin18sui_a.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/image/Mpf9JAonH9rTjG', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
