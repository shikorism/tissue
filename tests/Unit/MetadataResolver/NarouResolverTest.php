<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\NarouResolver;
use Tests\TestCase;

class NarouResolverTest extends TestCase
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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Narou/novel.html');

        $this->createResolver(NarouResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://novel18.syosetu.com/n2978fx/');
        $this->assertEquals('ぱんつ売りの少女', $metadata->title);
        $this->assertEquals("作者: 飴宮　地下\n冴えない男「ハルノ・ヒトキ」が仕事からの帰宅途中で美少女に声を掛けられる。\n驚いた事に少女の目的は自分の下着を売る事だった。\nお金に困っていた少女は徐々にヒトキに下着を売っていたが段々とその内容がエスカレートして行き……。", $metadata->description);
        $this->assertEquals(['ギャグ', '男主人公', '現代', '下着', '匂いフェチ', 'ブルセラ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://novel18.syosetu.com/novelview/infotop/ncode/n2978fx/', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
