<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\NijieResolver;
use Tests\TestCase;

class NijieResolverTest extends TestCase
{
    public function testStandardPicture()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://nijie.info/view.php?id=66384');
        $this->assertEquals('チンポップくんの日常ep.1「チンポップくんと釣り」 | ニジエ運営', $metadata->title);
        $this->assertEquals("メールマガジン漫画のバックナンバー第一話です！\r\n最新話はメールマガジンより配信中です。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
    }

    public function testMultiplePicture()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://nijie.info/view.php?id=202707');
        $this->assertEquals('ニジエ壁紙 | ニジエ運営', $metadata->title);
        $this->assertEquals("ニジエのPCとiphone用(4.7inch推奨)の壁紙です。\r\n保存してご自由にお使いくださいませ。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
    }

    public function testAnimationGif()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://nijie.info/view.php?id=9537');
        $this->assertEquals('ニジエがgifに対応したんだってね　奥さん | 黒末アプコ', $metadata->title);
        $this->assertEquals("アニメgifとか専門外なのでよくわかりませんでした", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
    }

    public function testMp4Movie()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://nijie.info/view.php?id=256283');
        $this->assertEquals('てすと | ニジエ運営', $metadata->title);
        $this->assertEquals("H264動画てすと　あとで消します\r\n\r\n今の所、H264コーデックのみ、出力時に音声なしにしないと投稿できません\r\n動画は勝手にループします", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
    }

    public function testStandardPictureSp()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://sp.nijie.info/view.php?id=66384');
        $this->assertEquals('チンポップくんの日常ep.1「チンポップくんと釣り」 | ニジエ運営', $metadata->title);
        $this->assertEquals("メールマガジン漫画のバックナンバー第一話です！\r\n最新話はメールマガジンより配信中です。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
    }

    public function testMultiplePictureSp()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://sp.nijie.info/view.php?id=202707');
        $this->assertEquals('ニジエ壁紙 | ニジエ運営', $metadata->title);
        $this->assertEquals("ニジエのPCとiphone用(4.7inch推奨)の壁紙です。\r\n保存してご自由にお使いくださいませ。", $metadata->description);
        $this->assertRegExp('/pic\d+\.nijie\.info/', $metadata->image);
        $this->assertNotRegExp('~/diff/main/~', $metadata->image);
    }

    public function testAnimationGifSp()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://nijie.info/view.php?id=9537');
        $this->assertEquals('ニジエがgifに対応したんだってね　奥さん | 黒末アプコ', $metadata->title);
        $this->assertEquals("アニメgifとか専門外なのでよくわかりませんでした", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
    }

    public function testMp4MovieSp()
    {
        sleep(1);
        $resolver = new NijieResolver();

        $metadata = $resolver->resolve('https://sp.nijie.info/view.php?id=256283');
        $this->assertEquals('てすと | ニジエ運営', $metadata->title);
        $this->assertEquals("H264動画てすと　あとで消します\r\n\r\n今の所、H264コーデックのみ、出力時に音声なしにしないと投稿できません\r\n動画は勝手にループします", $metadata->description);
        $this->assertRegExp('~/nijie\.info/pic/logo~', $metadata->image);
    }
}
