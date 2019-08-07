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
        $this->assertSame('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f6b84a8f-053e-4ab6-bd6c-71276a4a9282/dcap4fc-6fd6359c-770b-4515-9e29-e99311d58d57.png/v1/fit/w_700,h_700,q_70,strp/sally__nox_by_bonchilo_dcap4fc-pre.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9MTE0MCIsInBhdGgiOiJcL2ZcL2Y2Yjg0YThmLTA1M2UtNGFiNi1iZDZjLTcxMjc2YTRhOTI4MlwvZGNhcDRmYy02ZmQ2MzU5Yy03NzBiLTQ1MTUtOWUyOS1lOTkzMTFkNThkNTcucG5nIiwid2lkdGgiOiI8PTEyODAifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6aW1hZ2Uub3BlcmF0aW9ucyJdfQ.zbw4e5eH0NafyMmhM15DKN1NjawSZBUwr2RWQWB7O3o', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://backend.deviantart.com/oembed?url=https://www.deviantart.com/bonchilo/art/Sally-Nox-743562408', (string) $this->handler->getLastRequest()->getUri());
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
