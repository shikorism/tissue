<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\ComipoResolver;
use Tests\TestCase;

class ComipoResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testProduct()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Comipo/testProduct.html');

        $this->createResolver(ComipoResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.comipo.app/product/BJ138581');
        $this->assertEquals('快楽ヒストリエ', $metadata->title);
        $this->assertEquals('著者: 火鳥' . PHP_EOL . '天地創造と原初の人類を描いた「創世編」をはじめ、英雄たちの偉業を大真面目に考証した正真正銘の学術コミック全15編。', $metadata->description);
        $this->assertEquals('https://img.comipo.app/modpub/images2/work/books/BJ139000/BJ138581_img_main.jpg', $metadata->image);
        $this->assertEquals(['おやじ', 'ギャグ', 'コメディ', '女王様/お姫様', '戦士', '歴史/時代物', '王子様/王子系', '着物/和服', '褐色/日焼け', '青年コミック'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.comipo.app/product/BJ138581', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
