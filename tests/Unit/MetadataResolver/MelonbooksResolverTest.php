<?php
declare(strict_types=1);

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\MelonbooksResolver;
use Tests\TestCase;

class MelonbooksResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testUncensored()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Melonbooks/testUncensored.html');

        $this->createResolver(MelonbooksResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.melonbooks.co.jp/detail/detail.php?product_id=1836264');
        $this->assertEquals('めいど・で・ろっく!', $metadata->title);
        $this->assertEquals(<<<'EOF'
サークル: MIX-ISM
サークルMIX-ISM「ぼっち・ざ・ろっく!」本 第2弾!
ワンマンライブのチケットが売れていない結束バンドは
ライブハウスでメイドカフェ&ライブをやることに。

SNSで宣伝したりオリジナルフードメニューを考えたり
星歌、きくり、PAさん大人チームもメイド服でお手伝い。

メインキャラ総出演のドタバタギャグコメディ!
はたしてライブは成功するのか…?

犬威赤彦先生がお贈りする、ぼ〇ろのハイテンションメイドコメディ本の登場です☆
チケットノルマが足りないならメイド服になればいいじゃないと言う流れがすでに面白い♪
ぼっちちゃんの写真を撮っている時の奇跡の一枚が素材の良さを存分に発揮していて可愛い☆
可愛いメイド姿でカッコいいライブもこなす結束バンドが最高な、極上の一冊をお楽しみ下さい♪
EOF
            , $metadata->description);
        $this->assertEquals('https://melonbooks.akamaized.net/user_data/packages/resize_image.php?image=212001385384.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.melonbooks.co.jp/detail/detail.php?product_id=1836264', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testCensored()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Melonbooks/testCensored.html');

        $this->createResolver(MelonbooksResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.melonbooks.co.jp/detail/detail.php?product_id=1789342');
        $this->assertEquals('血姫夜交　真祖の姫は発情しているっ!', $metadata->title);
        $this->assertEquals(<<<'EOF'
サークル: 毛玉牛乳
毛玉牛乳オリジナル同人誌新シリーズ「血姫夜交」です。
森の塔に封じられた真祖(吸血鬼)の姫と出会う少年のおはなし。
吸血鬼のプラズマはプライドが高くいたずら好き。
チョロインの人外少女といちゃらぶするシリーズです。

玉之けだま先生の描く、オリジナルシリーズ最新作のヒロインは妖艶な雰囲気の悪戯好きな真祖の吸血鬼です☆
森の塔で出会った吸血鬼の少女が少年のチ〇コをひんやりとした口でぱくりと食べる様子がエロい♪
口に精液を出された後に、とろとろになったマ〇コを差し出し挿入してもらうのを待つ彼女に興奮します☆
膣内にチンコを迎え入れて歓喜しながら全身で感じている様子が最高にエロい♪
イタズラ好き真祖の少女がチ〇コに簡単に落とされるのがエロい、極上の一冊を是非お手元でお楽しみ下さい♪
EOF
            , $metadata->description);
        $this->assertEquals('https://melonbooks.akamaized.net/user_data/packages/resize_image.php?image=212001380763.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.melonbooks.co.jp/detail/detail.php?product_id=1789342', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
