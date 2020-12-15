<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\KomifloResolver;
use Tests\TestCase;

class KomifloResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testComic()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Komiflo/comic.json');

        $this->createResolver(KomifloResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://komiflo.com/#!/comics/5490');
        $this->assertEquals('魔法少女とえっち物語', $metadata->title);
        $this->assertEquals('薙派 - メガストアα 19.07', $metadata->description);
        $this->assertEquals('https://t.komiflo.com/564_mobile_large_3x/contents/23a4cd530060b8607aa434f4b299b249e71a4d5c.jpg', $metadata->image);
        $this->assertEquals(['薙派', 'お姉さん', 'ショタ', 'ファンタジー', '巨乳',  '羞恥', '野外・露出'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.komiflo.com/content/id/5490', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testComicWithNoParents()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Komiflo/comicWithNoParents.json');

        $this->createResolver(KomifloResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://komiflo.com/#!/comics/3414');
        $this->assertEquals('生まれなおしプログラム', $metadata->title);
        $this->assertEquals('EROKI - ?', $metadata->description);
        $this->assertEquals('https://t.komiflo.com/564_mobile_large_3x/contents/71cfb83640aead3cdd35e4329c4e2f427606a11d.jpg', $metadata->image);
        $this->assertEquals(['EROKI', 'お姉さん', 'しつけ', 'オリジナル', 'ショートカット', '巨乳', '逆転'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://api.komiflo.com/content/id/3414', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
