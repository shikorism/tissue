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
        $this->assertStringStartsWith('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?jwt=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://media.ci-en.jp/private/attachment/creator/00002462/a7afd3b02a6d1caa6afe6a3bf5550fb6a42aefba686f17a0a2f63c97fd6867ab/image-800.jpg?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJrZXkiOiJqd3RhdXRoX3NlY18yMDIwT2N0IiwiaXNzIjoiaHR0cHM6XC9cL2NpLWVuLmRsc2l0ZS5jb21cLyIsInN1YiI6IjAwMDAwMDAwMDAwIiwiYXVkIjoiYTdhZmQzYjAyYTZkMWNhYTZhZmU2YTNiZjU1NTBmYjZhNDJhZWZiYTY4NmYxN2EwYTJmNjNjOTdmZDY4NjdhYiIsImV4cCI6MTYwMjk5NTIyMX0.bXUG2T6nXl4hdvsvt1wkIMvbbBdsKk-xbwB6SaxARZA', $metadata->image);
            $this->assertSame(1602995221, $metadata->expires_at->timestamp);
            $this->assertSame('https://ci-en.dlsite.com/creator/2462/article/87502', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWithNoTimestamp()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Cien/testWithNoTimestamp.html');
        $this->createResolver(CienResolver::class, $responseText);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parameter "jwt" not found. Image=https://ci-en.dlsite.com/assets/img/common/logo_Ci-en_R18.svg Source=https://ci-en.dlsite.com/');

        $this->resolver->resolve('https://ci-en.dlsite.com/');
    }
}
