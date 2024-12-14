<?php
declare(strict_types=1);

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\BoothResolver;
use Tests\TestCase;

class BoothResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testTechBooks()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Booth/testTechBooks.json');

        $this->createResolver(BoothResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://booth.pm/ja/items/5985756');
        $this->assertEquals('C104 LaboLife', $metadata->title);
        $description = <<<TEXT
        らぼちっく；げーと

        C104で頒布した新刊です。本文102ページ。

        収録記事
        01. この世はすべてDisplayPortで動いている
        02. TRexでおうちトラフィックジェネレータ生活
        03. ちょっと足し算を作ろうとしたら構文解析していた件
        04. 君は完璧で究極のパッケージマネージャー
        05. 東急線沿線住民500万人のなかに「髪の毛の本数が同じ人」がいる確率は何％ですか？
        06. かわいい3D AIキャラクターとおしゃべりしようサービスの開発
        07. Rye&uvで始めるPythonプロジェクト管理
        08. 合同記事＊便利なソフトウェア
        09. 路上飲酒のすゝめ
        10. Google を捨てよ、書を買おう
        11. とある疫病中の機械収集
        12. 電動工具沼へようこそ
        13. るねファーム2024
        14. 育児オタク Lv5
        15. プリパラの思い出～プリパラ10周年記念～
        16. かわいい靴履くためにリンパケアの資格取った
        17. 社会人だけど大学院いって学歴つけてみた
        18. 18か月のイラスト練習で読んだ書籍
        TEXT;
        $this->assertEquals($description, $metadata->description);
        $this->assertEquals('https://booth.pximg.net/8ed459a1-6f9a-43b1-ba18-1c53b7e3e91d/i/5985756/13588e22-224b-42c0-b4cd-51116df3eea9_base_resized.jpg', $metadata->image);
        $this->assertEquals([
            'AI',
            'C104',
            'C104新刊',
            'DisplayPort',
            'Golang',
            'Nix',
            'Python',
            'Rye',
            'TRex',
            'VOICEVOX',
            'goyacc',
            'らぼちっく；げーと',
            'イラスト',
            'トラフィックジェネレータ',
            'プリパラ',
            'リンパケア',
            '大学院',
            '家庭菜園',
            '小説・その他書籍',
            '技術書',
            '数学',
            '構文解析',
            '育児',
            '買い物',
            '電動工具',
            '音声合成',
            '音声認識',
            '飲酒',
        ], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://booth.pm/ja/items/5985756.json', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testMangaR18()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Booth/testMangaR18.json');

        $this->createResolver(BoothResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://booth.pm/ja/items/2124847');
        $this->assertEquals('けもみみメイドといちゃいちゃする本', $metadata->title);
        $description = <<<TEXT
        ドットエイト

        オリジナルのえっち漫画です。
        作者:さわやか鮫肌
        漫画34＋表紙、奥付イラスト
        1433×2024ピクセル
        JPG、PDF

        がんばるあなたの味方キキーモラが疲れたご主人を癒やします。
        いちゃいちゃえっちまんがです。
        けもみみメイドというなんちゃってファンタジーですが
        設定とか世界観とか深いことは気にせず楽しめる漫画になっていると思います。
        TEXT;
        $this->assertEquals($description, $metadata->description);
        $this->assertEquals('https://booth.pximg.net/8f405d91-66b0-41b2-8007-4fac9e5b3ec9/i/2124847/ddd5a53b-18f3-4785-a1e1-5a5893f7f422_base_resized.jpg', $metadata->image);
        $this->assertEquals([
            'オリジナル',
            'ドットエイト',
            '漫画',
            '漫画・マンガ',
        ], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://booth.pm/ja/items/2124847.json', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSubdomain()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Booth/testSubdomain.json');

        $this->createResolver(BoothResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://adahemas.booth.pm/items/2124847');
        $this->assertEquals('けもみみメイドといちゃいちゃする本', $metadata->title);
        $description = <<<TEXT
        ドットエイト

        オリジナルのえっち漫画です。
        作者:さわやか鮫肌
        漫画34＋表紙、奥付イラスト
        1433×2024ピクセル
        JPG、PDF

        がんばるあなたの味方キキーモラが疲れたご主人を癒やします。
        いちゃいちゃえっちまんがです。
        けもみみメイドというなんちゃってファンタジーですが
        設定とか世界観とか深いことは気にせず楽しめる漫画になっていると思います。
        TEXT;
        $this->assertEquals($description, $metadata->description);
        $this->assertEquals('https://booth.pximg.net/8f405d91-66b0-41b2-8007-4fac9e5b3ec9/i/2124847/ddd5a53b-18f3-4785-a1e1-5a5893f7f422_base_resized.jpg', $metadata->image);
        $this->assertEquals([
            'オリジナル',
            'ドットエイト',
            '漫画',
            '漫画・マンガ',
        ], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://booth.pm/ja/items/2124847.json', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
