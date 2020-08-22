<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\HentaiFoundryResolver;
use Tests\TestCase;

class HentaiFoundryResolverTest extends TestCase
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
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/HentaiFoundry/illust.html');

        $this->createResolver(HentaiFoundryResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.hentai-foundry.com/pictures/user/DevilHS/723498/Witchcraft');
        $this->assertSame('Witchcraft', $metadata->title);
        $this->assertSame('by DevilHS' . PHP_EOL . 'gift for Liru', $metadata->description);
        $this->assertEquals(['witch', 'futa'], $metadata->tags);
        $this->assertSame('https://pictures.hentai-foundry.com/d/DevilHS/723498/DevilHS-723498-Witchcraft.png', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.hentai-foundry.com/pictures/user/DevilHS/723498/Witchcraft?enterAgree=1', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
