<?php
declare(strict_types=1);

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\MelonbooksResolver;
use Tests\TestCase;

class MelonbooksResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testUncensored()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Melonbooks/testUncensored.html');

        $this->createResolver(MelonbooksResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.melonbooks.co.jp/detail/detail.php?product_id=1836264');
        $this->assertEquals('めいど・で・ろっく!', $metadata->title);
        $this->assertEquals('サークル: MIX-ISM', $metadata->description);
        $this->assertEquals('https://melonbooks.akamaized.net/user_data/packages/resize_image.php?image=212001385384.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.melonbooks.co.jp/detail/detail.php?product_id=1836264', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testCensored()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Melonbooks/testCensored.html');

        $this->createResolver(MelonbooksResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.melonbooks.co.jp/detail/detail.php?product_id=1789342');
        $this->assertEquals('血姫夜交　真祖の姫は発情しているっ!', $metadata->title);
        $this->assertEquals('サークル: 毛玉牛乳', $metadata->description);
        $this->assertEquals('https://melonbooks.akamaized.net/user_data/packages/resize_image.php?image=212001380763.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.melonbooks.co.jp/detail/detail.php?product_id=1789342', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
