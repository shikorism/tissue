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

        $url = 'https://ecchi.iwara.tv/videos/wqlwatgmvhqg40kg';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('Cakeface【鈴谷、プリンツ】', $metadata->title);
        $this->assertEquals('投稿者: kuro@vov' . PHP_EOL . 'Thank you for watching!いつもありがとうございます' . PHP_EOL . 'こっそり微修正…' . PHP_EOL . 'Model：鈴谷&プリンツ　つみだんご様　罪袋：BCD様' . PHP_EOL . '（いずれも改変）クレジット漏れゴメンナサイ。。。' . PHP_EOL, $metadata->description);
        $this->assertEquals(['KanColle', 'kuro@vov'], $metadata->tags);
        $this->assertEquals('https://i.iwara.tv/sites/default/files/videos/thumbnails/238591/thumbnail-238591_0004.jpg', $metadata->image);
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
        $this->assertEquals('投稿者: kochira' . PHP_EOL . 'Ray-cast test. Still trying to figure out how Ray-cast works so I\'m sorry if anything looks off.' . PHP_EOL . 'Unauthorized reproduction prohibited (無断転載は禁止です／未經授權禁止複製)' . PHP_EOL, $metadata->description);
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
        $this->assertEquals('投稿者: Tonjiru Lion' . PHP_EOL . '今回はあんまエロくないです。' . PHP_EOL, $metadata->description);
        $this->assertEquals(['Vocaloid', 'Tonjiru Lion'], $metadata->tags);
        $this->assertEquals('https://i.iwara.tv/sites/default/files/photos/jing_yin_rin18sui_a.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame($url, (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
