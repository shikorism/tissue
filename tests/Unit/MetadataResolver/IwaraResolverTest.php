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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/video.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://www.iwara.tv/video/rdBNaAPKwS3AjZ';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('すーぱー☆あふぇくしょん - そに子 / Super Affection - Sonico', $metadata->title);
        $this->assertEquals('投稿者: ミュー' . PHP_EOL . '高画質版、別視点などはこちらに' . PHP_EOL . 'https://fantia.jp/fanclubs/246278' . PHP_EOL . 'https://www.patreon.com/mu_mmd', $metadata->description);
        $this->assertEquals(['bikini', 'blender', 'dance', 'mikumikudance', 'super_sonico', 'user2234567'], $metadata->tags);
        $this->assertEquals('https://files.iwara.tv/image/original/b2c987ae-206a-4018-b6de-39aca962a272/thumbnail-09.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/video/rdBNaAPKwS3AjZ', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testVideoOldUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/video.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://ecchi.iwara.tv/videos/rdBNaAPKwS3AjZ';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('すーぱー☆あふぇくしょん - そに子 / Super Affection - Sonico', $metadata->title);
        $this->assertEquals('投稿者: ミュー' . PHP_EOL . '高画質版、別視点などはこちらに' . PHP_EOL . 'https://fantia.jp/fanclubs/246278' . PHP_EOL . 'https://www.patreon.com/mu_mmd', $metadata->description);
        $this->assertEquals(['bikini', 'blender', 'dance', 'mikumikudance', 'super_sonico', 'user2234567'], $metadata->tags);
        $this->assertEquals('https://files.iwara.tv/image/original/b2c987ae-206a-4018-b6de-39aca962a272/thumbnail-09.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/video/rdBNaAPKwS3AjZ', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testVideoUrlYoutubeCom()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/youtube-com.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://iwara.tv/videos/6e0loc1av2hjkjknn';
        $metadata = $this->resolver->resolve($url);
        $this->assertEquals('MMD Genshin impact R18 - Eula - Jessi zoom', $metadata->title);
        $this->assertEquals('投稿者: Gangz' . PHP_EOL . 'R18 high quality ver: https://www.patreon.com/posts/69513286' . PHP_EOL . 'R18 720p ver: https://www.patreon.com/posts/69513481', $metadata->description);
        $this->assertEquals(['dance', 'game', 'genshin_impact', 'sex', 'gangz'], $metadata->tags);
        $this->assertEquals('https://img.youtube.com/vi/4jY5KaIMAWQ/maxresdefault.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.iwara.tv/video/6e0loc1av2hjkjknn', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testVideoUrlYoutuBe()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/youtu-be.json');

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

    public function testImage()
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

    public function testImageOldUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Iwara/image.json');

        $this->createResolver(IwaraResolver::class, $responseText);

        $url = 'https://iwara.tv/images/Mpf9JAonH9rTjG/';
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
