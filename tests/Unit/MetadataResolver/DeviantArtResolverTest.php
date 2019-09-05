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

    public function testWixmp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/DeviantArt/wixmp.json');

        $this->createResolver(DeviantArtResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.deviantart.com/bonchilo/art/Sally-Nox-743562408');
        $this->assertSame('Sally  Nox', $metadata->title);
        $this->assertSame('By Bonchilo', $metadata->description);
        $this->assertStringStartsWith('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f6b84a8f-053e-4ab6-bd6c-71276a4a9282/dcap4fc-6fd6359c-770b-4515-9e29-e99311d58d57.png/v1/fit/w_1024,h_1024,strp/image.jpg?token=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/bonchilo/art/Sally-Nox-743562408', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testWixmpNoImageOptions()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/DeviantArt/wixmpNoImageOptions.json');

        $this->createResolver(DeviantArtResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.deviantart.com/messenger-lame/art/rem-639676105');
        $this->assertSame('rem', $metadata->title);
        $this->assertSame('By messenger-lame', $metadata->description);
        $this->assertStringStartsWith('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/9afa7937-381f-47f0-a8bc-40b9db1faad1/dakuh8p-aea3fc1c-c06e-466b-88ba-d27be8e164e9.png/v1/fit/w_1024,h_1024,strp/image.jpg?token=', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/messenger-lame/art/rem-639676105', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMature()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/DeviantArt/mature.json');

        $this->createResolver(DeviantArtResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.deviantart.com/rasbii/art/backstage-620617246');
        $this->assertSame('backstage', $metadata->title);
        $this->assertSame('By Rasbii', $metadata->description);
        $this->assertSame('https://orig00.deviantart.net/eb50/f/2016/191/a/b/preview_by_rasbii-da9hzby.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/rasbii/art/backstage-620617246', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
