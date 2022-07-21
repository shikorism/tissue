<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\PlurkResolver;
use Tests\TestCase;

class PlurkResolverTest extends TestCase
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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Plurk/test.html');

        $this->createResolver(PlurkResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.plurk.com/p/n0awli/');
        $this->assertEquals('[R18]FC2實況中', $metadata->title);
        $this->assertMatchesRegularExpression('/Plurk by 小虫.+ - \d+ response\(s\)/', $metadata->description);
        $this->assertEquals('https://images.plurk.com/5cT15Sf9OOFYk9fEQ759bZ.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.plurk.com/p/n0awli/', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWithExtenalLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Plurk/testWithExtenalLink.html');

        $this->createResolver(PlurkResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.plurk.com/p/ombe6f/');
        $this->assertEquals('[R18] [R-18]Halloween 萬剩J', $metadata->title);
        $this->assertMatchesRegularExpression('/Plurk by 013 - \d+ response\(s\)/', $metadata->description);
        $this->assertEquals('https://images.plurk.com/6H1y4AtplqIppG4gLMMGwu.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.plurk.com/p/ombe6f/', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
