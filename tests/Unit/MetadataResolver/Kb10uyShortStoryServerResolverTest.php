<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\Kb10uyShortStoryServerResolver;
use Tests\TestCase;

class Kb10uyShortStoryServerResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testNormalPost()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/Kb10uyShortStoryServer/tomone.html');

        $this->createResolver(Kb10uyShortStoryServerResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://ss.kb10uy.org/posts/14');
        $this->assertSame('朋音「は、はぁ？おむつ？」', $metadata->title);
        $this->assertSame('自炊したおかずってやつです。とりあえずこのSSの中ではkb10uyの彼女は朋音ってことにしといてください。そうじゃないと出す男が決定できないので。', $metadata->description);
        $this->assertSame(['妄想', 'kb10uy', '岩永朋音', 'おむつ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://ss.kb10uy.org/posts/14', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
