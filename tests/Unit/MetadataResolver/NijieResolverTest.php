<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\NijieResolver;
use Tests\TestCase;

class NijieResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testStandardPicture()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPicture.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=66384');
        $this->assertEquals('チンポップくんの日常ep.1「チンポップくんと釣り」 | ニジエ運営', $metadata->title);
        $this->assertEquals("メールマガジン漫画のバックナンバー第一話です！\r\n最新話はメールマガジンより配信中です。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMultiplePicture()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMultiplePicture.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=202707');
        $this->assertEquals('ニジエ壁紙 | ニジエ運営', $metadata->title);
        $this->assertEquals("ニジエのPCとiphone用(4.7inch推奨)の壁紙です。\r\n保存してご自由にお使いくださいませ。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=202707', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAnimationGif()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testAnimationGif.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=9537');
        $this->assertEquals('ニジエがgifに対応したんだってね　奥さん | 黒末アプコ', $metadata->title);
        $this->assertEquals('アニメgifとか専門外なのでよくわかりませんでした', $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=9537', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMp4Movie()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMp4Movie.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=256283');
        $this->assertEquals('てすと | ニジエ運営', $metadata->title);
        $this->assertEquals("H264動画てすと　あとで消します\r\n\r\n今の所、H264コーデックのみ、出力時に音声なしにしないと投稿できません\r\n動画は勝手にループします", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=256283', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testStandardPictureSp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPictureSp.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view.php?id=66384');
        $this->assertEquals('チンポップくんの日常ep.1「チンポップくんと釣り」 | ニジエ運営', $metadata->title);
        $this->assertEquals("メールマガジン漫画のバックナンバー第一話です！\r\n最新話はメールマガジンより配信中です。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMultiplePictureSp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMultiplePictureSp.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view.php?id=202707');
        $this->assertEquals('ニジエ壁紙 | ニジエ運営', $metadata->title);
        $this->assertEquals("ニジエのPCとiphone用(4.7inch推奨)の壁紙です。\r\n保存してご自由にお使いくださいませ。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=202707', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAnimationGifSp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testAnimationGifSp.html');

        $this->createResolver(NijieResolver::class, $responseText);


        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=9537');
        $this->assertEquals('ニジエがgifに対応したんだってね　奥さん | 黒末アプコ', $metadata->title);
        $this->assertEquals('アニメgifとか専門外なのでよくわかりませんでした', $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=9537', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMp4MovieSp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMp4MovieSp.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view.php?id=256283');
        $this->assertEquals('てすと | ニジエ運営', $metadata->title);
        $this->assertEquals("H264動画てすと　あとで消します\r\n\r\n今の所、H264コーデックのみ、出力時に音声なしにしないと投稿できません\r\n動画は勝手にループします", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=256283', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
