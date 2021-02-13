<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\SteamResolver;
use Tests\TestCase;

class SteamResolverTest extends TestCase
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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Steam/test.json');

        $this->createResolver(SteamResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://store.steampowered.com/app/333600');
        $this->assertEquals('NEKOPARA Vol. 1', $metadata->title);
        $this->assertEquals('水無月嘉祥(みなづき かしょう)は伝統ある老舗和菓子屋である実家を出て、 パティシエとして自身のケーキ屋『ラ・ソレイユ』を一人で開店する。 しかし実家から送った引っ越し荷物の中に、 実家で飼っていた人型ネコのショコラとバニラが紛れ込んでいた。', $metadata->description);
        $this->assertStringStartsWith('https://cdn.akamai.steamstatic.com/steam/apps/333600/header.jpg?t=', $metadata->image);
    }

    public function testR18()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Steam/testR18.json');

        $this->createResolver(SteamResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://store.steampowered.com/app/1077580');
        $this->assertEquals('Broke Girl  | 負債千金', $metadata->title);
        $this->assertEquals('苦労知らずに育ったお嬢様は一夜にして1000万の借金を背負うことになった。借金を返済するために働かなければならない。しかし世間には悪意が満ちており、男達はお金で彼女を誘うか凌辱することしか考えていない。', $metadata->description);
        $this->assertStringStartsWith('https://cdn.akamai.steamstatic.com/steam/apps/1077580/header.jpg?t=', $metadata->image);
    }

    public function testNotFound()
    {
        $this->expectException(\RuntimeException::class);

        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Steam/testNotFound.json');

        $this->createResolver(SteamResolver::class, $responseText);

        $this->resolver->resolve('https://store.steampowered.com/app/1');
    }
}
