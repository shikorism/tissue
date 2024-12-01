<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\NarouResolver;
use Tests\TestCase;

class NarouResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testNovel()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Narou/novel.html');

        $this->createResolver(NarouResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://novel18.syosetu.com/n2978fx/');
        $this->assertEquals('ぱんつ売りの少女', $metadata->title);
        $this->assertEquals("作者: 飴宮　地下\n冴えない男「ハルノ・ヒトキ」が仕事からの帰宅途中で美少女に声を掛けられる。\n驚いた事に少女の目的は自分の下着を売る事だった。\nお金に困っていた少女は徐々にヒトキに下着を売っていたが段々とその内容がエスカレートして行き……。", $metadata->description);
        $this->assertEquals(['ギャグ', '男主人公', '現代', '下着', '匂いフェチ', 'ブルセラ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://novel18.syosetu.com/novelview/infotop/ncode/n2978fx/', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testNovelViewURL()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Narou/novel.html');

        $this->createResolver(NarouResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://novel18.syosetu.com/novelview/infotop/ncode/n2978fx/');
        $this->assertEquals('ぱんつ売りの少女', $metadata->title);
        $this->assertEquals("作者: 飴宮　地下\n冴えない男「ハルノ・ヒトキ」が仕事からの帰宅途中で美少女に声を掛けられる。\n驚いた事に少女の目的は自分の下着を売る事だった。\nお金に困っていた少女は徐々にヒトキに下着を売っていたが段々とその内容がエスカレートして行き……。", $metadata->description);
        $this->assertEquals(['ギャグ', '男主人公', '現代', '下着', '匂いフェチ', 'ブルセラ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://novel18.syosetu.com/novelview/infotop/ncode/n2978fx/', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testLongDescriptionNovel()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Narou/novel_longdescription.html');

        $this->createResolver(NarouResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://novel18.syosetu.com/n0477gn/');
        $this->assertEquals('俺のことが好き過ぎる双子の天才妹が、「妹と結婚して子作りすること」を合法化して二人がかりで精液を搾りとってくる話', $metadata->title);
        $this->assertEquals("作者: 伍式\n巨大財閥の跡取りである高校生「菊川笙(きくかわしょう)」。その双子の妹、貧乳美尻丁寧語の「菊川琴(こと)」と巨乳巨尻甘えん坊の「菊川鈴(すず)」。\n巨大財閥を実質的に動かしている天才双子妹は、しかし兄を盲愛して兄の精液で孕みたいと固く決意しているダブルブラコン妹だった。\nある時妹たち二人と一緒に高級……", $metadata->description);
        $this->assertEquals(['ほのぼの', '男主人公', '妹', 'いちゃらぶ', '近親相姦', 'オナニー', '見せつけ', '尻合わせ', '孕ませっくす', '自慰', '尻ズリ', '兄妹', '双子', 'だいしゅきホールド', '膝立ちバック'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://novel18.syosetu.com/novelview/infotop/ncode/n0477gn/', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testFeaturesInKeyword()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Narou/testFeaturesInKeyword.html');

        $this->createResolver(NarouResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://novel18.syosetu.com/n6404hk/');
        $this->assertEquals('王子繁殖促進計画「王子の性欲を刺激せよ」　～よってたかって性欲を刺激される小国王子の異世界奮闘記～', $metadata->title);
        $this->assertEquals("作者: ひもの\n小国だが王子に転生した俺。　\n意外に甘くない現実に戸惑いつつも、いつかはエロエロハーレムが作れたらいいな、と思っていたが……　\n\n幼馴染が、いとこのお姉ちゃんが、周りの美少女達が、恥じらいながらも一生懸命、俺の性欲を刺激してくる。　\n\nえ？　俺の性欲を刺激するために、国家プロジェクトが立ち上がった？……", $metadata->description);
        $this->assertEquals(['残酷な描写あり', '異世界転生', '男主人公', 'ハーレム', '魔法', 'オリジナル戦記', '戦記', '淫語', '羞恥', '美少女', 'イチャラブ', '♡喘ぎ', '孕ませ', '巨乳'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://novel18.syosetu.com/novelview/infotop/ncode/n6404hk/', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
