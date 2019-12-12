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
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/04/nijie_picture/38_20131130155623.png', $metadata->image);
        $this->assertSame(['ニジエたん', '釣り', 'チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMultiplePicture()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMultiplePictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=202707');
        $this->assertSame('ニジエ壁紙', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'ニジエのPCとiphone用(4.7inch推奨)の壁紙です。' . PHP_EOL . '保存してご自由にお使いくださいませ。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/03/nijie_picture/38_20170209185801_0.png', $metadata->image);
        $this->assertSame(['ニジエたん', '壁紙'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=202707', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAnimationGif()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testAnimationGifResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=9537');
        $this->assertSame('ニジエがgifに対応したんだってね　奥さん', $metadata->title);
        $this->assertSame('投稿者: 黒末アプコ' . PHP_EOL . 'アニメgifとか専門外なのでよくわかりませんでした', $metadata->description);
        $this->assertStringStartsWith('https://nijie.info/pic/logo/nijie_logo_og.png', $metadata->image);
        $this->assertSame(['おっぱい', '陥没乳首', '眼鏡', 'GIFアニメ', 'ぶるんぶるん', 'アニメgif'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=9537', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMp4Movie()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testMp4MovieResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=256283');
        $this->assertSame('てすと', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'H264動画てすと　あとで消します' . PHP_EOL .  PHP_EOL . '今の所、H264コーデックのみ、出力時に音声なしにしないと投稿できません' . PHP_EOL . '動画は勝手にループします', $metadata->description);
        $this->assertStringStartsWith('https://nijie.info/pic/logo/nijie_logo_og.png', $metadata->image);
        $this->assertSame([], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=256283', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testViewPopup()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view_popup.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/04/nijie_picture/38_20131130155623.png', $metadata->image);
        $this->assertSame(['ニジエたん', '釣り', 'チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSp()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/04/nijie_picture/38_20131130155623.png', $metadata->image);
        $this->assertSame(['ニジエたん', '釣り', 'チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSpViewPopup()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view_popup.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/04/nijie_picture/38_20131130155623.png', $metadata->image);
        $this->assertSame(['ニジエたん', '釣り', 'チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testHasHtmlInAuthorProfile()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Nijie/testHasHtmlInAuthorProfileResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=285698');
        $this->assertSame('ＪＫ文化祭コスプレ喫茶', $metadata->title);
        $this->assertSame('投稿者: ままままま' . PHP_EOL .
            'https://www.pixiv.net/fanbox/creator/32045169' . PHP_EOL .
            'ピクシブのファンボックスでこっちに上げてた一次創作のノリでえっちなやつ描いてます' . PHP_EOL .
            '二次創作のえっちなやつは相変わらずこっち' . PHP_EOL . '健全目なのはついったー', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie_picture/540086_20181028112046_0.png', $metadata->image);
        $this->assertSame(['バニーガール'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=285698', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
