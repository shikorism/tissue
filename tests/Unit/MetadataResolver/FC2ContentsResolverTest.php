<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\FC2ContentsResolver;
use Tests\TestCase;

class FC2ContentsResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testAdult()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/FC2Contents/adult.html');

        $this->createResolver(FC2ContentsResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://adult.contents.fc2.com/article_search.php?id=401545');
        $this->assertEquals('個人撮影＠「ぱいずりオアトリート♡」Jカップ魔女っ子の３連挟射しても続けちゃうパイズリ！', $metadata->title);
        $this->assertEquals('個人撮影＠「ぱいずりオアトリート♡」Jカップ魔女っ子の３連挟射しても続けちゃうパイズリ！ -         イベントコスチュームということもあり、大ボリュームだった前回、前々回の パイズリ役Ｊcupメイド と ナースパイズリを超え 今回さらに超ボリューム＆超密度の内容になってます！   --------             …', $metadata->description);
        $this->assertEquals('https://storage2000.contents.fc2.com/file/104/10362633/1477676255.72.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://adult.contents.fc2.com/article_search.php?id=401545', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testGeneral()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/FC2Contents/general.html');

        $this->createResolver(FC2ContentsResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://contents.fc2.com/article_search.php?id=336610');
        $this->assertEquals('ゆかいなどうぶつたち　～オオカミ・キツネ・タヌキ～', $metadata->title);
        $this->assertEquals('ゆかいなどうぶつたち　～オオカミ・キツネ・タヌキ～ - 今回のおともだちは、オオカミ・キツネ・タヌキだよ。地球上に住んでいるたくさんのおともだち、みんなにどんどん紹介するからたのしみにしてね！', $metadata->description);
        $this->assertEquals('https://storage6000.contents.fc2.com/file/300/29917555/1519118184.65.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://contents.fc2.com/article_search.php?id=336610', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
