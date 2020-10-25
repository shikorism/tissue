<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\DeviantArtResolver;
use Tests\TestCase;

class DeviantArtResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testMature()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DeviantArt/mature.json');

        $this->createResolver(DeviantArtResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.deviantart.com/gatanii69/art/R-15-mabel-and-will-update-686016962');
        $this->assertSame('R-15 mabel and will update', $metadata->title);
        $this->assertSame('By gatanii69', $metadata->description);
        $this->assertStringStartsWith('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/6854f36d-8010-4cd0-9d62-0cf9b7829764/dbcfq2q-d78c9f6e-dced-4e5c-a345-2a1bfd5d7620.jpg', $metadata->image);
        $this->assertSame(['nsfw', 'reversefalls', 'gravityfalls', 'gravityfallsfanart', 'mabelpines', 'billcipher', 'reversemabel', 'willcipher', 'reversebill', 'reversebillcipher', 'mawill'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/gatanii69/art/R-15-mabel-and-will-update-686016962', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
