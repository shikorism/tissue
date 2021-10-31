<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\MGStageResolver;
use Tests\TestCase;

class MGStageResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function test()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/MGStage/test.html');

        $this->createResolver(MGStageResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.mgstage.com/product/product_detail/420HOI-083/');
        $this->assertSame('一夏(22) 素人ホイホイZ・素人・受付嬢感・美人・女子アナ感・九州美人・美少女・清楚・美乳・顔射・ハメ撮り', $metadata->title);
        $this->assertSame('[＃受付嬢感＃美人＃女子アナ感＃九州美人]「普通過ぎるのがコンプレックスで。」…？何を言っているのでしょう、この子は。全女子敵に回すレベル。クソ美人です。まぁ全男子が味方になるので問題ないんですが。九州で受付のお仕事を辞めて、上京。やはり美人の仕事は受付が相場ですね。とかいうとフェミ厨五月蠅い昨今ですが、美人は正義です。ただ居るだけで透明感もあり、中身までも清楚にした女子アナって感じの雰囲気ですが、ワンチャンは狙わねばなりません。(使命感)おなかが空いているのか、パクパク食べる姿も無邪気で、もはや喫茶店ごと買い与えてしまいそうなレベルですが、コンビニで酒を買いホテルへGO。下ネタにも引かず、正直に話してくれる感なりますが、普通に経験は少なめの様子。モデルテイで見せてもらったカラダは色白で手足が長く、まさにモデルさんのよう。決して経験は多くないのかもしれませんが、経験の少なさと感度の良さは別！というか、感度が良すぎる。恥じらいか興奮か、色白肌が火照ってピンクになるエロ漫画みたいな展開。華奢なカラダを突き回して、長い脚を震わせ、崩れないように歯を食いしばって悶える立ちバック最高です。恥じらいで、必死で抑え気味の喘ぎ声がドンドン大きくなっていくエロ漫画のような(ry)。この美貌、このエロスは本当にヤバいです！(語彙力)', $metadata->description);
        $this->assertSame('https://image.mgstage.com/images/hoihoiz/420hoi/083/pb_e_420hoi-083.jpg', $metadata->image);
        $this->assertSame(['ハメ撮り', '清楚', '素人', '美乳', '美少女', '配信専用', '顔射'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.mgstage.com/product/product_detail/420HOI-083/', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
