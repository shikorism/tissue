<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\OGPResolver;
use GuzzleHttp\Exception\BadResponseException;
use Tests\TestCase;

class OGPResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function testMissingUrl()
    {
        $this->createResolver(OGPResolver::class, '', [], 404);

        $this->expectException(BadResponseException::class);
        $this->resolver->resolve('http://example.com/404');
    }

    public function testResolve()
    {
        $response = <<< 'HTML'
<!DOCTYPE html>
<html>
  <head prefix="og: https://ogp.me/ns#">
    <meta charset="utf-8">
    <title>The Open Graph protocol</title>
    <meta name="description" content="The Open Graph protocol enables any web page to become a rich object in a social graph.">
    <link rel="stylesheet" href="base.css" type="text/css">
    <meta property="og:title" content="Open Graph protocol">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ogp.me/">
    <meta property="og:image" content="https://ogp.me/logo.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="300">
    <meta property="og:image:height" content="300">
    <meta property="og:image:alt" content="The Open Graph logo">
    <meta property="og:description" content="The Open Graph protocol enables any web page to become a rich object in a social graph.">
    <meta prefix="fb: https://ogp.me/ns/fb#" property="fb:app_id" content="115190258555800">
    <link rel="alternate" type="application/rdf+xml" href="https://ogp.me/ns/ogp.me.rdf">
    <link rel="alternate" type="text/turtle" href="https://ogp.me/ns/ogp.me.ttl">
  </head>
  <body></body>
</html>
HTML;
        $this->createResolver(OGPResolver::class, $response);

        $resolver = $this->createResolver(OGPResolver::class, $response);
        $metadata = $resolver->resolve('https://ogp.me');
        $this->assertEquals('Open Graph protocol', $metadata->title);
        $this->assertEquals('The Open Graph protocol enables any web page to become a rich object in a social graph.', $metadata->description);
        $this->assertEquals('https://ogp.me/logo.png', $metadata->image);
    }

    public function testResolveTitleOnly()
    {
        $response = <<< 'HTML'
<!doctype html>
<html>
<head>
    <title>Example Domain</title>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body></body>
</html>
HTML;
        $this->createResolver(OGPResolver::class, $response);

        $metadata = $this->resolver->resolve('http://example.com');
        $this->assertEquals('Example Domain', $metadata->title);
        $this->assertEmpty($metadata->description);
        $this->assertEmpty($metadata->image);
    }

    public function testResolveTitleAndDescription()
    {
        $resolver = $this->app->make(OGPResolver::class);

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
