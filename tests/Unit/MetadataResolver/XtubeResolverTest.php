<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\XtubeResolver;
use Tests\TestCase;

class XtubeResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function test()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Xtube/test.json');

        $this->createResolver(XtubeResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.xtube.com/video-watch/homegrown-big-tits-18634762');
        $this->assertEquals('Homegrown Big Tits', $metadata->title);
        $this->assertEquals('Dedicated to the fans of the beautiful amateur women with big natural tits.  All user submitted - you can see big boob amateur hotties fucking and sucking as their tits bounce and sway.', $metadata->description);
        $this->assertRegExp('~https://cdn\d+-s-hw-e5\.xtube\.com/m=eSK08f/videos/201302/07/RF4Nk-S774-/240X180/1\.jpg~', $metadata->image);
        $this->assertEquals(['bigtits', 'homeg'], $metadata->tags);
    }

    public function testNotMatch()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unmatched URL Pattern: https://www.xtube.com/gallery/black-celebs-free-7686657');

        $this->createResolver(XtubeResolver::class, '');
        $this->resolver->resolve('https://www.xtube.com/gallery/black-celebs-free-7686657');
    }

    public function testNotOK()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('404: https://www.xtube.com/video-watch/notfound-404');

        $this->createResolver(XtubeResolver::class, '', [], 404);
        $this->resolver->resolve('https://www.xtube.com/video-watch/notfound-404');
    }
}
