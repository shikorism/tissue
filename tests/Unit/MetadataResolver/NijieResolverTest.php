<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\NijieResolver;
use Tests\TestCase;

class NijieResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testStandardPicture()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie/13/38/38/illust/0_0_1d558c0f2e86887d_b80e30.png', $metadata->image);
        $this->assertSame(['チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMultiplePicture()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testMultiplePictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=202707');
        $this->assertSame('ニジエ壁紙', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'ニジエのPCとiphone用(4.7inch推奨)の壁紙です。' . PHP_EOL . '保存してご自由にお使いくださいませ。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie/17/38/38/illust/0_0_9fc29dcac80a60fd_f01e8d.png', $metadata->image);
        $this->assertSame(['ニジエたん', '壁紙'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=202707', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAnimationGif()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testAnimationGifResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=393134');
        $this->assertSame('MANKALOさん', $metadata->title);
        $this->assertStringStartsWith('投稿者: sb' . PHP_EOL . 'すけぶの。服の着脱機能を勝手に付けさせて頂きました。', $metadata->description);
        $this->assertStringStartsWith('https://nijie.info/pic/logo/nijie_logo_og.png', $metadata->image);
        $this->assertSame(['ロリ', '中出し', 'フェラ', '騎乗位', '獣耳', 'GIFアニメ', 'ドット絵'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=393134', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMp4Movie()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testMp4MovieResponse.html');

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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view_popup.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie/13/38/38/illust/0_0_1d558c0f2e86887d_b80e30.png', $metadata->image);
        $this->assertSame(['チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSp()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie/13/38/38/illust/0_0_1d558c0f2e86887d_b80e30.png', $metadata->image);
        $this->assertSame(['チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSpViewPopup()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://sp.nijie.info/view_popup.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/nijie/13/38/38/illust/0_0_1d558c0f2e86887d_b80e30.png', $metadata->image);
        $this->assertSame(['チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testHasHtmlInAuthorProfile()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testHasHtmlInAuthorProfileResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=285698');
        $this->assertSame('ＪＫ文化祭コスプレ喫茶', $metadata->title);
        $this->assertSame('投稿者: ままままま' . PHP_EOL .
            'https://www.pixiv.net/fanbox/creator/32045169' . PHP_EOL .
            'ピクシブのファンボックスでこっちに上げてた一次創作のノリでえっちなやつ描いてます' . PHP_EOL .
            '二次創作のえっちなやつは相変わらずこっち' . PHP_EOL . '健全目なのはついったー', $metadata->description);
        $this->assertSame('https://pic.nijie.net/08/nijie/18/86/540086/illust/0_0_85bddb31a5218f20_6c0c7f.png', $metadata->image);
        $this->assertSame(['バニーガール'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=285698', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
