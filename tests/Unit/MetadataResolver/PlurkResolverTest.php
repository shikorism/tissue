<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\PlurkResolver;
use Tests\TestCase;

class PlurkResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp()
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function test()
    {
        $responseText = file_get_contents(__DIR__ . '/../../fixture/Plurk/test.html');

        $this->createResolver(PlurkResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.plurk.com/p/n0awli/');
        $this->assertEquals('[R18]FC2實況中', $metadata->title);
        $this->assertEquals('Plurk by 小虫/ムシ@台中種 - 71 response(s)', $metadata->description);
        $this->assertEquals('https://images.plurk.com/5cT15Sf9OOFYk9fEQ759bZ.jpg', $metadata->image);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.plurk.com/p/n0awli/', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
