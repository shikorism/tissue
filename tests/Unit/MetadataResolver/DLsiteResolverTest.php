<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\DLsiteResolver;
use Tests\TestCase;

class DLsiteResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testProduct()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/DLsite/testProduct.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/maniax/work/=/product_id/RJ171695.html');
        $this->assertEquals('【骨伝導風】道草屋 たびらこ-一緒にはみがき【耳かき&はみがき】', $metadata->title);
        $this->assertStringEndsWith('少しお母さんっぽい店員さんに、歯磨きからおやすみまでお世話されます。はみがきで興奮しちゃった旦那様のも、しっかりお世話してくれます。歯磨き音は特殊なマイクを使用、骨伝導風ハイレゾバイノーラル音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ172000/RJ171695_img_main.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/maniax/work/=/product_id/RJ171695.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testProductSP()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/DLsite/testProductSP.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home/work/=/product_id/RJ234446.html');
        $this->assertEquals('【大人向け耳かき】道草屋 はこべら5 時計修理のはこべらさん。他【汗の匂い】', $metadata->title);
        $this->assertStringEndsWith('夏の終わり、二人で遠くの花火を眺めます。耳かきの他、クラシックシェービング、氷を含んだあまがみ、冷紅茶、ジャズ、時計の修理、それから大人向けの汗の匂い。色々な事のある、二泊三日の田舎宿音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ235000/RJ234446_img_main.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ234446.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
