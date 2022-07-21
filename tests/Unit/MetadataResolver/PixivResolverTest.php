<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\PixivResolver;
use Tests\TestCase;

class PixivResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testIllust()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Pixiv/illust.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=68188073');
        $this->assertEquals('coffee break', $metadata->title);
        $this->assertStringStartsWith('投稿者: 裕', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-master/img/2018/04/12/00/01/28/68188073_p0_master1200.jpg', $metadata->image);
        $this->assertEquals(['オリジナル', 'カフェ', '眼鏡', 'イヤホン', 'ぱっつん', '艶ぼくろ', '眼鏡っ娘', 'マニキュア', '赤セーター', 'オリジナル7500users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/68188073', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testIllustMultiPages()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Pixiv/illustMultiPages.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=75899985');
        $this->assertEquals('コミッション絵33', $metadata->title);
        $this->assertEquals('投稿者: ナゼ(NAZE)' . PHP_EOL . 'Leak' . PHP_EOL . PHP_EOL . 'Character:アリッサさん（依頼主のオリキャラ）', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-master/img/2019/07/25/13/02/59/75899985_p0_master1200.jpg', $metadata->image);
        $this->assertEquals(['巨乳輪', '超乳', '巨乳首', '母乳'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/75899985', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testManga()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Pixiv/manga.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=46713544');
        $this->assertEquals('冬の日ラブラブ', $metadata->title);
        $this->assertEquals('投稿者: Aza' . PHP_EOL . 'ラブラブエッチのらくがき' . PHP_EOL . PHP_EOL . '三万フォロワー感謝します～' . PHP_EOL . PHP_EOL . '最近忙しいので、自分の時間が少ない・・・', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-master/img/2014/10/25/00/06/58/46713544_p0_master1200.jpg', $metadata->image);
        $this->assertEquals(['落書き', 'おっぱい', 'オリジナル', 'パイズリ', '中出し', '愛のあるセックス', 'だいしゅきホールド', '黒髪ロング', 'オリジナル30000users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/46713544', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testArtworkUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Pixiv/illust.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/artworks/68188073');
        $this->assertEquals('coffee break', $metadata->title);
        $this->assertStringStartsWith('投稿者: 裕', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-master/img/2018/04/12/00/01/28/68188073_p0_master1200.jpg', $metadata->image);
        $this->assertEquals(['オリジナル', 'カフェ', '眼鏡', 'イヤホン', 'ぱっつん', '艶ぼくろ', '眼鏡っ娘', 'マニキュア', '赤セーター', 'オリジナル7500users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/68188073', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testArtworkUrlEn()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Pixiv/illust.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/en/artworks/68188073');
        $this->assertEquals('coffee break', $metadata->title);
        $this->assertStringStartsWith('投稿者: 裕', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-master/img/2018/04/12/00/01/28/68188073_p0_master1200.jpg', $metadata->image);
        $this->assertEquals(['オリジナル', 'カフェ', '眼鏡', 'イヤホン', 'ぱっつん', '艶ぼくろ', '眼鏡っ娘', 'マニキュア', '赤セーター', 'オリジナル7500users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/68188073', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
