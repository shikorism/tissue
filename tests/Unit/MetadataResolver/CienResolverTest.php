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
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Cien/test.html');

        $this->createResolver(CienResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ci-en.dlsite.com/creator/2462/article/87502');
        $this->assertSame('進捗とボツ立ち絵', $metadata->title);
        $this->assertSame('ドット製２D ACTを製作しています。' . PHP_EOL . '恐ろしい存在に襲われる絶望感や、被虐的な官能がテーマです。', $metadata->description);
        $this->assertStringStartsWith('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?px-time=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?px-time=1568231879&px-hash=70c57e9a73d5afb4ac5363d1f37a851af8e0cb1f', $metadata->image);
            $this->assertSame(1568235479, $metadata->expires_at->timestamp);
            $this->assertSame('https://ci-en.dlsite.com/creator/2462/article/87502', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWithNoTimestamp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Cien/testWithNoTimestamp.html');
        $this->createResolver(CienResolver::class, $responseText);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parameter "px-time" not found. Image=https://ci-en.dlsite.com/assets/img/common/logo_Ci-en_R18.svg Source=https://ci-en.dlsite.com/');

        $this->resolver->resolve('https://ci-en.dlsite.com/');
    }
}
