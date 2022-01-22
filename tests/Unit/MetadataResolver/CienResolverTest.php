<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\CienResolver;
use Tests\TestCase;

class CienResolverTest extends TestCase
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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Cien/test.html');

        $this->createResolver(CienResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ci-en.dlsite.com/creator/2462/article/87502');
        $this->assertSame('進捗とボツ立ち絵 - ねんない５ - Ci-en（シエン）', $metadata->title);
        $this->assertSame('今日のサムネイルはストアページに掲載する予定のキャラクター紹介画像です。 ドットでない解像度の高いイラストは時間も体力も精神力もかかるので、こういうのを行うタスクを開発終盤に残さないでよかったと本気……', $metadata->description);
        $this->assertStringStartsWith('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?px-time=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?px-time=1636410222&px-hash=707b609a8e0be2a64cc71370bb50d49554952308', $metadata->image);
            $this->assertSame(1636413822, $metadata->expires_at->timestamp);
            $this->assertSame('https://ci-en.dlsite.com/creator/2462/article/87502', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWithNoPostImage()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Cien/testWithNoPostImage.html');

        $this->createResolver(CienResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ci-en.dlsite.com/creator/148/article/401866');
        $this->assertSame('近況報告 - 薄稀 - Ci-en（シエン）', $metadata->title);
        $this->assertSame('サキュバスをはじめ、M向けの魔物娘をよく描くエロ絵描きです(´ω｀) 近況報告 - Ci-en（シエン）', $metadata->description);
        $this->assertSame('https://media.ci-en.jp/public/cover/creator/00000148/9153a13f78591bc2c9efae1021a26f9b90d24d3b30a0b3e699d0050f09dab6df/image-990-c.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertNull($metadata->expires_at);
            $this->assertSame('https://ci-en.dlsite.com/creator/148/article/401866', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
