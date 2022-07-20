<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\PixivNovelResolver;
use Tests\TestCase;

class PixivNovelResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testNovel()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/PixivNovel/15768044.json');

        $this->createResolver(PixivNovelResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.pixiv.net/novel/show.php?id=15768044');
        $this->assertEquals('研究用の精液サンプルを集めるためにふたなりアイドルをとっ捕まえて射精させていく志希の話', $metadata->title);
        $this->assertEquals("投稿者: banana\nこういうのされてみたいです", $metadata->description);
        $this->assertEquals(['アイドルマスターシンデレラガールズ', 'ふたなり', '一ノ瀬志希', '塩見周子', '橘ありす', '鷺沢文香', '二宮飛鳥', 'アイマス小説100users入り'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.pixiv.net/ajax/novel/15768044', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
