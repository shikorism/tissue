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
        $this->assertSame('https://pic.nijie.net/02/__s_rs_l0x0/9ae9d6be25e5583296cfeba2a7e7fc723c67ad9147e3556fd17bcac1104b474f28a536e1aef69ccc7126e1edf836a7d46a.png', $metadata->image);
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
        $this->assertSame('https://pic.nijie.net/02/__s_rs_l0x0/9ae9d6be25e5583696cfeba2a7e7fc723c67ad9147e3556fd17bc2c3464c46487ba267bca6a192c42026e1e9f037a7df3e.png', $metadata->image);
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
        $this->assertSame(['ロリ', '騎乗位', 'GIFアニメ', 'ドット絵'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=393134', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMp4Movie()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testMp4MovieResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=355403');
        $this->assertSame('ぱこぱこ', $metadata->title);
        $this->assertSame(<<<EOS
        投稿者: 色谷あすか
        <font color="#ff0000">支援サイトにてサキュバスちゃんと焦らし→種付けセックスする動画を公開中です！ボイス＆SEつきは2月限定公開、長さは2分超です！</font>
        ◆Fantia→https://fantia.jp/posts/278812
        ◆FANBOX→https://www.pixiv.net/fanbox/creator/3188698/post/808575

        ◆サキュバスちゃんの新刊委託先
        メロン：https://www.melonbooks.co.jp/circle/index.php?circle_id=23444
        とら：https://ec.toranoana.jp/tora_r/ec/item/040030793849/
        BOOTH：https://aoirokanata.booth.pm/items/1805620
        DMM：https://www.dmm.co.jp/dc/doujin/-/detail/=/cid=d_167803/aokana-005
        DLsite：https://www.dlsite.com/maniax/dlaf/=/t/s/link/work/aid/aonizi/id/RJ276825.html
        EOS, $metadata->description);
        $this->assertStringStartsWith('https://nijie.info/pic/logo/nijie_logo_og.png', $metadata->image);
        $this->assertSame(['おっぱい', '貧乳', 'ロリ', 'サキュバス', 'ニーソ', '腋', '創作', 'おへそ', '動画', 'パイパン', 'アニメーション', '淫紋'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=355403', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testViewPopup()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testStandardPictureResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view_popup.php?id=66384');
        $this->assertSame('チンポップくんの日常ep.1「チンポップくんと釣り」', $metadata->title);
        $this->assertSame('投稿者: ニジエ運営' . PHP_EOL . 'メールマガジン漫画のバックナンバー第一話です！' . PHP_EOL . '最新話はメールマガジンより配信中です。', $metadata->description);
        $this->assertSame('https://pic.nijie.net/02/__s_rs_l0x0/9ae9d6be25e5583296cfeba2a7e7fc723c67ad9147e3556fd17bcac1104b474f28a536e1aef69ccc7126e1edf836a7d46a.png', $metadata->image);
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
        $this->assertSame('https://pic.nijie.net/02/__s_rs_l0x0/9ae9d6be25e5583296cfeba2a7e7fc723c67ad9147e3556fd17bcac1104b474f28a536e1aef69ccc7126e1edf836a7d46a.png', $metadata->image);
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
        $this->assertSame('https://pic.nijie.net/02/__s_rs_l0x0/9ae9d6be25e5583296cfeba2a7e7fc723c67ad9147e3556fd17bcac1104b474f28a536e1aef69ccc7126e1edf836a7d46a.png', $metadata->image);
        $this->assertSame(['チンポップ君の日常', '公式漫画'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=66384', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testHasHtmlInAuthorProfile()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Nijie/testHasHtmlInAuthorProfileResponse.html');

        $this->createResolver(NijieResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://nijie.info/view.php?id=514492');
        $this->assertSame('全肯定手コキ', $metadata->title);
        $this->assertSame('投稿者: ままままま' . PHP_EOL .
            'ゲーム中で聞けるボイスそのままのセリフで' . PHP_EOL .
            'ブルアカの先生は生徒でしこってそう', $metadata->description);
        $this->assertSame('https://pic.nijie.net/08/__s_rs_l0x0/9ae9d6be25e55b33c8c8fcb5a2f0e62f603be0d41ca5095c94578f8a15214f732bf162b2a0f9c1917e77dabff360a38305835c845eeb00.png', $metadata->image);
        $this->assertSame(['ブルーアーカイブ', 'ブルアカ', '一之瀬アスナ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://nijie.info/view.php?id=514492', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
