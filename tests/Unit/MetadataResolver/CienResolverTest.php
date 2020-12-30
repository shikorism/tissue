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
        $this->assertSame('進捗とボツ立ち絵 - ねんない５ - Ci-en', $metadata->title);
        $this->assertSame('ドット製２D ACTを製作しています。' . PHP_EOL . '恐ろしい存在に襲われる絶望感や、被虐的な官能がテーマです。', $metadata->description);
        $this->assertStringStartsWith('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-web.jpg?jwt=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-web.jpg?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJrZXkiOiJqd3RhdXRoX3NlY18yMDIwT2N0IiwiaXNzIjoiaHR0cHM6XC9cL2NpLWVuLmRsc2l0ZS5jb21cLyIsInN1YiI6IjAwMDAwMDAwMDAwIiwiYXVkIjoiYTdhZmQzYjAyYTZkMWNhYTZhZmU2YTNiZjU1NTBmYjZhNDJhZWZiYTY4NmYxN2EwYTJmNjNjOTdmZDY4NjdhYiIsImV4cCI6MTYwNzA2NzMyOX0.-462_WtZ6AUOxrfndBE-0_oWHKwesP9mMMn6K2oYQJM', $metadata->image);
            $this->assertSame(1607067329, $metadata->expires_at->timestamp);
            $this->assertSame('https://ci-en.dlsite.com/creator/2462/article/87502', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWithNoPostImage()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Cien/testWithNoPostImage.html');

        $this->createResolver(CienResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ci-en.dlsite.com/creator/148/article/401866');
        $this->assertSame('近況報告 - 薄稀 - Ci-en', $metadata->title);
        $this->assertSame('サキュバスをはじめ、M向けの魔物娘をよく描くエロ絵描きです(´ω｀)', $metadata->description);
        $this->assertSame('https://media.ci-en.jp/public/cover/creator/00000148/9153a13f78591bc2c9efae1021a26f9b90d24d3b30a0b3e699d0050f09dab6df/image-990-c.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertNull($metadata->expires_at);
            $this->assertSame('https://ci-en.dlsite.com/creator/148/article/401866', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
