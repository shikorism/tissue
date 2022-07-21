<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\ToranoanaResolver;
use Tests\TestCase;

class ToranoanaResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testTora()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testTora.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ecs.toranoana.jp/tora/ec/item/040030720152');
        $this->assertEquals('新・古明地喫茶～そしてまた扉は開く～', $metadata->title);
        $this->assertEquals('東方Projectの現代パロディ作品。心が読める店長 古明地さとりが経営する喫茶店のお話。', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ecs.toranoana.jp/tora/ec/item/040030720152', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraR()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testToraR.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/tora_r/ec/item/040030720174');
        $this->assertEquals('お姉ちゃんが妹のぱんつでひとりえっちしてました。', $metadata->title);
        $this->assertEquals('妹のぱんつでオナニーしてしまう変態シスコンお姉ちゃんと、ちょっぴりツンツン気味の気難しいお年頃の妹の百合えっち本です。', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/tora_r/ec/item/040030720174', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraD()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testToraD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ecs.toranoana.jp/tora_d/digi/item/042000013358');
        $this->assertEquals('虎の穴ラボの薄い本。vol 1.5', $metadata->title);
        $this->assertEquals('12ページ4記事を追加しバージョンアップ！ 虎の穴エンジニアの専門部署である【虎の穴ラボ】制作の、「虎の穴ラボの薄い本。vol1.5」です！日頃、技術ブログや社内外の勉強会で発表しているラボメンが書い', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ecs.toranoana.jp/tora_d/digi/item/042000013358', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testToraRD()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testToraRD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/tora_rd/digi/item/042000013181');
        $this->assertEquals('放課後のお花摘み', $metadata->title);
        $this->assertEquals('女の子がおしっこするだけの漫画です', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/tora_rd/digi/item/042000013181', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshi()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testJoshi.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ecs.toranoana.jp/joshi/ec/item/040030702729');
        $this->assertEquals('円卓のクソ漫画', $metadata->title);
        $this->assertEquals('「なんでも許すからとりあえず仲良さげな円卓が見たい」という方には全力でお勧めするギャグ漫画。散歩番組とか某長寿お笑い番組のパロディを含みます。', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ecs.toranoana.jp/joshi/ec/item/040030702729', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiR()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testJoshiR.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/joshi_r/ec/item/040030730126');
        $this->assertEquals('リバースナイトリバース', $metadata->title);
        $this->assertEquals('いつもみかに抱かれている宗。突然宗が攻めに興味を持ってやってみたいといいだして？！みかは戸惑いながら受け入れようとするけど？！※リバではない。みか宗要素しかありません。', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/joshi_r/ec/item/040030730126', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiD()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testJoshiD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ecs.toranoana.jp/joshi_d/digi/item/042000012192');
        $this->assertEquals('超幸運ガール審神者GOLDEN', $metadata->title);
        $this->assertEquals('刀をすれば一発目で三日月を、刀装を作れば世紀末ヒャッハーだったりガン〇ムだったりな超幸運の少女審神者のギャグ漫画です。ラブコメ要素はほぼありませんがみんな審神者が大好き。過去出したシリーズ3冊の再録本', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ecs.toranoana.jp/joshi_d/digi/item/042000012192', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testJoshiRD()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Toranoana/testJoshiRD.html');

        $this->createResolver(ToranoanaResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ec.toranoana.jp/joshi_rd/digi/item/042000013472');
        $this->assertEquals('UBWの裏側で非公式に遠坂凛をナデナデする本', $metadata->title);
        $this->assertEquals('遠坂凛ちゃんをナデナデして魔力供給をする話。ＵＢＷ原作の展開に沿ってナデナデ内容が変化していきます。アーチャーと凛ちゃんは挿入行為なし。衛宮士郎と凛ちゃんはすけべします。', $metadata->description);
        $this->assertMatchesRegularExpression('~ecdnimg\.toranoana\.jp/ec/img/.*\.jpg~', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ec.toranoana.jp/joshi_rd/digi/item/042000013472', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
