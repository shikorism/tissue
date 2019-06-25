<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\PixivResolver;
use Tests\TestCase;

class PixivResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testIllust()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/Pixiv/illust.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=68188073');
        $this->assertEquals('coffee break', $metadata->title);
        $this->assertEquals('投稿者: 裕' . PHP_EOL, $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-original/img/2018/04/12/00/01/28/68188073_p0.jpg', $metadata->image);
        $this->assertEquals(['オリジナル','カフェ','眼鏡','イヤホン','ぱっつん','艶ぼくろ','眼鏡っ娘','オリジナル5000users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/68188073', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testIllustMultiPages()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/Pixiv/illustMultiPages.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=74939802');
        $this->assertEquals('T-20S', $metadata->title);
        $this->assertEquals('投稿者: amssc' . PHP_EOL . 'JUST FOR FUN' . PHP_EOL . '现在可以做到游戏内立绘修改拉！立绘动态皮肤都可以支持，想要资助获得新技术请站内信联系我。', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-original/img/2019/05/28/01/16/24/74939802_p0.jpg', $metadata->image);
        $this->assertEquals(['巨乳','母乳','lastorigin','Last_Origin','T-20S','おっぱい','라스트오리진','노움'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/74939802', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testManga()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/Pixiv/manga.json');

        $this->createResolver(PixivResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=46713544');
        $this->assertEquals('冬の日ラブラブ', $metadata->title);
        $this->assertEquals('投稿者: Aza' . PHP_EOL . 'ラブラブエッチのらくがき' . PHP_EOL . PHP_EOL . '三万フォロワー感謝します～' . PHP_EOL . PHP_EOL . '最近忙しいので、自分の時間が少ない・・・', $metadata->description);
        $this->assertEquals('https://i.pixiv.cat/img-original/img/2014/10/25/00/06/58/46713544_p0.jpg', $metadata->image);
        $this->assertEquals(['落書き','おっぱい','オリジナル','パイズリ','中出し','だいしゅきホールド','愛のあるセックス','黒髪ロング','オリジナル10000users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/illust/46713544', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
