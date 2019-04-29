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

    public function testProductSPLink()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/DLsite/testProduct.html');
        // SP版（touch）のURLのテストだがリゾルバ側でURLから-touchを削除してPC版を取得するので、PC版の内容を使用する

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/maniax-touch/work/=/product_id/RJ171695.html');
        $this->assertEquals('【骨伝導風】道草屋 たびらこ-一緒にはみがき【耳かき&はみがき】', $metadata->title);
        $this->assertStringEndsWith('少しお母さんっぽい店員さんに、歯磨きからおやすみまでお世話されます。はみがきで興奮しちゃった旦那様のも、しっかりお世話してくれます。歯磨き音は特殊なマイクを使用、骨伝導風ハイレゾバイノーラル音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ172000/RJ171695_img_main.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/maniax/work/=/product_id/RJ171695.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testProductShortLink()
    {
        $responseText = file_get_contents(__DIR__.'/../../fixture/DLsite/testProduct.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://dlsite.jp/mawtw/RJ171695.html');
        $this->assertEquals('【骨伝導風】道草屋 たびらこ-一緒にはみがき【耳かき&はみがき】', $metadata->title);
        $this->assertStringEndsWith('少しお母さんっぽい店員さんに、歯磨きからおやすみまでお世話されます。はみがきで興奮しちゃった旦那様のも、しっかりお世話してくれます。歯磨き音は特殊なマイクを使用、骨伝導風ハイレゾバイノーラル音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ172000/RJ171695_img_main.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://dlsite.jp/mawtw/RJ171695.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
