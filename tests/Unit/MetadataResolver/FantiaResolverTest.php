<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\FantiaResolver;
use Tests\TestCase;

class FantiaResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function test()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Fantia/test.json');

        $this->createResolver(FantiaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://fantia.jp/posts/206561');
        $this->assertSame('召喚士アルドラ', $metadata->title);
        $this->assertSame('サークル: サークルぬるま湯 (ナナナナ)' . PHP_EOL . 'コミッション' . PHP_EOL . 'クイーンズブレイドリベリオンの召喚士アルドラです。', $metadata->description);
        $this->assertSame('https://c.fantia.jp/uploads/post/file/206561/main_dbcc59e5-4090-4650-b969-8855a721c6a5.jpg', $metadata->image);
        $this->assertSame(['ふたなり', '超乳', '超根', 'クイーンズブレイド', 'ナナナナ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://fantia.jp/api/v1/posts/206561', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
