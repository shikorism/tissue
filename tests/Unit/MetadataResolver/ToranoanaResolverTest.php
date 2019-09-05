<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\ToranoanaResolver;
use Tests\TestCase;

class ToranoanaResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testTora()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testTora.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.shop/tora/ec/item/040030720152');
        $this->assertEquals('新・古明地喫茶～そしてまた扉は開く～', $metadata->title);
        $this->assertEquals('サークル【ツキギのとこ】（槻木こうすけ）発行の「新・古明地喫茶～そしてまた扉は開く～」を買うなら、とらのあな全年齢向け通信販売！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.shop/tora/ec/item/040030720152', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraR()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testToraR.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/tora_r/ec/item/040030720174');
        $this->assertEquals('お姉ちゃんが妹のぱんつでひとりえっちしてました。', $metadata->title);
        $this->assertEquals('サークル【没後】（RYO）発行の「お姉ちゃんが妹のぱんつでひとりえっちしてました。」を買うなら、とらのあな成年向け通信販売！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/tora_r/ec/item/040030720174', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraD()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testToraD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.shop/tora_d/digi/item/042000013358');
        $this->assertEquals('虎の穴ラボの薄い本。vol 1.5', $metadata->title);
        $this->assertEquals('サークル【虎の穴ラボ】（虎の穴ラボエンジニアチーム）発行の「虎の穴ラボの薄い本。vol 1.5」を買うなら、とらのあな全年齢向け電子書籍！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.shop/tora_d/digi/item/042000013358', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraRD()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testToraRD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/tora_rd/digi/item/042000013181');
        $this->assertEquals('放課後のお花摘み', $metadata->title);
        $this->assertEquals('サークル【給食泥棒】（村雲）発行の「放課後のお花摘み」を買うなら、とらのあな成年向け電子書籍！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/tora_rd/digi/item/042000013181', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshi()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testJoshi.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.shop/joshi/ec/item/040030702729');
        $this->assertEquals('円卓のクソ漫画', $metadata->title);
        $this->assertEquals('サークル【地獄のすなぎもカーニバル】（槌田）発行の「円卓のクソ漫画」を買うなら、とらのあなJOSHIBU全年齢向け通信販売！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.shop/joshi/ec/item/040030702729', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiR()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testJoshiR.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/joshi_r/ec/item/040030730126');
        $this->assertEquals('リバースナイトリバース', $metadata->title);
        $this->assertEquals('サークル【雨傘サイクル】（チャリリズム）発行の「リバースナイトリバース」を買うなら、とらのあなJOSHIBU成年向け通信販売！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/joshi_r/ec/item/040030730126', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiD()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testJoshiD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.shop/joshi_d/digi/item/042000012192');
        $this->assertEquals('超幸運ガール審神者GOLDEN', $metadata->title);
        $this->assertEquals('サークル【Day Of The Dead】（ほんちゅ）発行の「超幸運ガール審神者GOLDEN」を買うなら、とらのあなJOSHIBU全年齢向け電子書籍！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.shop/joshi_d/digi/item/042000012192', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiRD()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Toranoana/testJoshiRD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/joshi_rd/digi/item/042000013472');
        $this->assertEquals('UBWの裏側で非公式に遠坂凛をナデナデする本', $metadata->title);
        $this->assertEquals('サークル【阿仁谷組】（阿仁谷ユイジ）発行の「UBWの裏側で非公式に遠坂凛をナデナデする本」を買うなら、とらのあなJOSHIBU成年向け電子書籍！', $metadata->description);
        $this->assertRegExp('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/joshi_rd/digi/item/042000013472', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
