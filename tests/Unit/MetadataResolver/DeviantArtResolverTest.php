<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\DeviantArtResolver;
use Tests\TestCase;

class DeviantArtResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testMature()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/DeviantArt/mature.json');

        $this->createResolver(DeviantArtResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.deviantart.com/rasbii/art/backstage-620617246');
        $this->assertSame('backstage', $metadata->title);
        $this->assertSame('By Rasbii', $metadata->description);
        $this->assertStringStartsWith('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/bee96c7a-4956-44a4-81aa-7587cf0b85d1/da9hzby-b9640ac3-1815-4c77-9410-0d4882a20e25.png/v1/fit/w_1024,h_1024,strp/image.jpg?token=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/rasbii/art/backstage-620617246', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
