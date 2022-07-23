<?php
declare(strict_types=1);

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\TwitterApiResolver;
use Tests\TestCase;

class TwitterApiResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            $this->markTestSkipped('このテストケースは、実リクエストを伴うテストには対応していません');
        }
    }

    public function test()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/TwitterApi/text.json');

        $this->createResolver(TwitterApiResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://twitter.com/jidan_no_jouken/status/1549688068721876998');
        $this->assertEquals('黒塗りの高級車に追突してしまうbot', $metadata->title);
        $this->assertEquals(
            "簡単なことも解らないわ あたしって何だっけ\n" .
            "それすら夜の手に絆されて 愛のように消える\n" .
            "さようならも言えぬ儘 泣いたフォニイ \n" .
            "嘘に絡まっている黒ヌリイ\n" .
            "\n" .
            "の高級車に追突してしまう\n" .
            "後輩をかばいすべての責任を負った三浦に対し、\n" .
            '車の主、暴力団員谷岡が言い渡した示談の条件とは…。',
            $metadata->description
        );
        $this->assertEmpty($metadata->image);
    }

    public function testPicture()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/TwitterApi/multiple_pictures.json');

        $this->createResolver(TwitterApiResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://twitter.com/grassleaf/status/1550454315659436032');
        $this->assertEquals('grassleaf', $metadata->title);
        $this->assertEquals('https://t.co/HDzQNuHC8d', $metadata->description);
        $this->assertEquals('https://pbs.twimg.com/media/FYRR9cPaIAEtfyk.jpg', $metadata->image);
    }

    public function testPictureWithIndex()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/TwitterApi/multiple_pictures.json');

        $this->createResolver(TwitterApiResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://twitter.com/grassleaf/status/1550454315659436032/photo/2');
        $this->assertEquals('grassleaf', $metadata->title);
        $this->assertEquals('https://t.co/HDzQNuHC8d', $metadata->description);
        $this->assertEquals('https://pbs.twimg.com/media/FYRR93aacAEv-F6.jpg', $metadata->image);
    }

    public function testVideo()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/TwitterApi/video.json');

        $this->createResolver(TwitterApiResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://twitter.com/grassleaf/status/1550454508349964288');
        $this->assertEquals('grassleaf', $metadata->title);
        $this->assertEquals('https://t.co/dnYmaU6hcB', $metadata->description);
        $this->assertEquals('https://pbs.twimg.com/ext_tw_video_thumb/1550454471712722945/pu/img/_yjYc6ZmB6NbfRDO.jpg', $metadata->image);
    }
}
