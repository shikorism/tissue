<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\OGPResolver;
use GuzzleHttp\Exception\ClientException;
use Tests\TestCase;

class OGPResolverTest extends TestCase
{
    public function testMissingUrl()
    {
        $resolver = new OGPResolver();

        $this->expectException(ClientException::class);
        $resolver->resolve('http://example.com/404');
    }

    public function testResolve()
    {
        $resolver = new OGPResolver();

        $metadata = $resolver->resolve('http://ogp.me');
        $this->assertEquals('Open Graph protocol', $metadata->title);
        $this->assertEquals('The Open Graph protocol enables any web page to become a rich object in a social graph.', $metadata->description);
        $this->assertEquals('http://ogp.me/logo.png', $metadata->image);
    }

    public function testResolveTitleOnly()
    {
        $resolver = new OGPResolver();

        $metadata = $resolver->resolve('http://example.com');
        $this->assertEquals('Example Domain', $metadata->title);
        $this->assertEmpty($metadata->description);
        $this->assertEmpty($metadata->image);
    }

    public function testResolveTitleAndDescription()
    {
        $resolver = new OGPResolver();

        $html = <<<EOF
<title>Welcome to my homepage</title>
<meta name="description" content="This is my super hyper ultra homepage!!" />
EOF;

        $metadata = $resolver->parse($html);
        $this->assertEquals('Welcome to my homepage', $metadata->title);
        $this->assertEquals('This is my super hyper ultra homepage!!', $metadata->description);
        $this->assertEmpty($metadata->image);
    }
}
