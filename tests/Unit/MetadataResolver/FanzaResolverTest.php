<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\FanzaResolver;
use Tests\TestCase;

class FanzaResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    /**
     * @dataProvider provider
     */
    public function test($filename, $url, $title, $description, $image, $tags)
    {
        $responseText = $this->fetchSnapshot(__DIR__ . "/../../fixture/Fanza/{$filename}");

        $this->createResolver(FanzaResolver::class, $responseText);

        $metadata = $this->resolver->resolve($url);
        $this->assertSame($title, $metadata->title);
        $this->assertSame($description, $metadata->description);
        $this->assertSame($image, $metadata->image);
        $this->assertSame($tags, $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame($url, (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function provider()
    {
        return [
            '動画 digital/videoa' => [
                'digital_videoa.html',
                'https://www.dmm.co.jp/digital/videoa/-/detail/=/cid=ssis00341/',
                '「先生のフェラのほうが気持ち良いよ？」 彼女ができた僕に嫉妬した痴女教師が執拗即尺で何度も寝取ろうとしてくる 羽咲みはる',
                '僕のことが大好きすぎるみはる先生。彼女が出来た事を先生に知られてしまいその日から僕は…。休み時間中、放課後に図書室などで会うたびにズボンを脱がして僕のチ●ポをしゃぶり、僕のことを寝取ろうとしてくるんです（汗）。彼女以上に余裕で上手くて気持ちイイ大人のフェラテク、そして僕が先生を好きになるまでとことん止めない追撃フェラ！即射確実のエロい淫口を何度も体験してしまったら…もう彼女では絶対ヌケない！',
                'https://pics.dmm.co.jp/digital/video/ssis00341/ssis00341pl.jpg',
                ['羽咲みはる', 'トレンディ山口', '彼女ができた僕に嫉妬した痴女●●が執拗即尺で何度も寝取ろうとしてくる', 'エスワン_ナンバーワンスタイル', 'S1_NO.1_STYLE', 'ハイビジョン', '4K', '独占配信', '痴女', '巨乳', '顔射', '単体作品', 'ギリモザ', 'フェラ']
            ],
            '素人動画 digital/videoc' => [
                'digital_videoc.html',
                'https://www.dmm.co.jp/digital/videoc/-/detail/=/cid=sweet015/',
                'ねる',
                '鉄板オナ素材的ハイシコリティ！もうサンプルは見ていただけましたか！？そうなんです！非の打ち所まるで無し！恋するキラッキラの瞳！愛嬌抜群の純真笑顔！Gカップ巨乳にむっちむちの恵体！モザイク越しにも伝わってしまう雑誌グラビア級の美少女ルックス！このスペックなのに自分に自信が持てない系のウブっ子！触れただけで濡れだす敏感ボディ！ねっとりDキスから嬉しそうに大量唾液をゴク飲みする程度には恋愛洗脳済み！溢れ出るガマン汁を丁寧に舐めとるラブいフェラ！ビックビク痙攣しながら困り顔で何度も何度も連続イキ絶頂！',
                'https://pics.dmm.co.jp/digital/amateur/sweet015/sweet015jp.jpg',
                ['素人ホイホイsweet！', '巨乳', '制服', '清楚', '美少女', '女子校生', 'ハイビジョン']
            ],
            'アニメ digital/anime' => [
                'digital_anime.html',
                'https://www.dmm.co.jp/digital/anime/-/detail/=/cid=h_1379jdxa57513/',
                '性活週間 THE ANIMATION 第1巻',
                'めちゃシコ美少女マスター・みちきんぐの初単行本が' . PHP_EOL . '『ヌーディストビーチに修学旅行で？』『リアルエロゲシチュエーション』など' . PHP_EOL . '大ヒットシリーズを手掛けたアダルトアニメ界の新進気鋭クリエイター' . PHP_EOL . '「小原和大」によって待望のOVA化！' . PHP_EOL .  PHP_EOL . '私と姉体験してみない？' . PHP_EOL . PHP_EOL . '（c）2019 みちきんぐ/GOT/ピンクパイナップル',
                'https://pics.dmm.co.jp/digital/video/h_1379jdxa57513/h_1379jdxa57513pl.jpg',
                ['性活週間_THE_ANIMATION', 'ピンクパイナップル', 'Pink_Pineapple', 'ハイビジョン', '中出し', 'フェラ', '巨乳', '姉・妹']
            ],
            '同人' => [
                'doujin.html',
                'https://www.dmm.co.jp/dc/doujin/-/detail/=/cid=d_115139/',
                '美少女拉致って性教育',
                'ハ○エースでおさげ髪美少女を拉致って、凌●する内容です。' . PHP_EOL . '汚っさん×美少女モノ。' . PHP_EOL . PHP_EOL . '表紙込み総ページ数28p（内本文27p）' . PHP_EOL . '表紙大きさ1200×1719' . PHP_EOL . '本文大きさ1200×1694',
                'https://doujin-assets.dmm.co.jp/digital/comic/d_115139/d_115139pr.jpg',
                ['美少女拉致って性教育', 'オリジナル', '制服', '男性向け', 'ミニ系', '少女', '屋外', '中出し', '成人向け', 'みくろぺえじ'],
            ],
            '電子書籍' => [
                'book.html',
                'https://book.dmm.co.jp/product/739007/b104atint00313/',
                '少女×少女×少女',
                '少女達が乱舞する…！天上家。俺が捨てたあの家…祭子から「母が亡くなった」と電話を受けて、俺は妹達を救うために帰って行くが…。そこで待っていたのは、運命に逆らえず妹達との果てしなき乱交の宴だった…。透明感溢れる魅力的なキャラクター、緻密に描きこまれた世界、そしてそのスタイルからは想像できないハードかつ長大なエロ描写！赤月みゅうとのセカンド単行本。',
                'https://ebook-assets.dmm.co.jp/digital/e-book/b104atint00313/b104atint00313pl.jpg',
                ['赤月みゅうと', 'MUJIN編集部', 'ティーアイネット', '単行本', '美少女', '中出し', '3P・4P', 'ハーレム']
            ],
            'PCゲーム' => [
                'dlsoft.html',
                'https://dlsoft.dmm.co.jp/detail/views_0630/',
                '姫と穢欲のサクリファイス【Windows10対応版】',
                'ソリデ国――国家間戦争に勝利し発展した大国は、一人の男によって襲撃される。国王に強い恨みを抱き、復讐のために行動を起こした主人公・カルドは使役している‘‘悪魔’’の力を借りて城を掌握。国政や国民には興味を示さず、国王への復讐として悪魔達の能力を使って王女・フィアナへの調教を開始する。',
                'https://pics.dmm.co.jp/digital/pcgame/views_0630/views_0630pl.jpg',
                ['B-銀河', '遊丸', '瑠奈璃亜', 'はっとりまさき', '蒼瀬', '木下じゃっく', '御導はるか', '薄迷', '犬童飛沫', '星天誠', '紅ぴえろ', '姫様調教シリーズ', 'お姫様', '辱め', 'SM', '淫乱', 'デモ・体験版あり', 'ファンタジー', 'ブラウザ対応', 'Windows10対応作品', 'CGがいい', 'エロに定評', 'キャラクターがいい', 'ゲーム性に定評', 'シナリオがいい', '誰でも20%ポイント還元キャンペーン']
            ],
            // '未対応' => [
            //     'nosupport.html',
            //     'http://www.dmm.co.jp/ppm/video/-/detail/=/cid=h_275tdsu00032/',
            //     '素人のお姉さん！！「チ○ポを洗う」お仕事してみませんか？ 2',
            //     'パーツモデルの募集と思い面接に訪れた素人娘達に、初めての『チ●ポ』を洗うお仕事してもらいました！『エッチとかじゃなくて…洗うだけなら…』自らに言い聞かせる様に出演承諾した彼女...',
            //     'http://pics.dmm.co.jp/digital/video/h_275tdsu00032/h_275tdsu00032pl.jpg',
            //     []
            // ]
        ];
    }
}
