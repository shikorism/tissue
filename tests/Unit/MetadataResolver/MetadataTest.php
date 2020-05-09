<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\Metadata;
use Tests\TestCase;

class MetadataTest extends TestCase
{
    public function testNormalizedTagsCanTrim()
    {
        $metadata = new Metadata();
        $metadata->tags = ['foo ', ' bar', ' foo bar '];

        $this->assertEquals(['foo', 'bar', 'foo bar'], $metadata->normalizedTags());
    }

    public function testNormalizedTagsCanSanitize()
    {
        $metadata = new Metadata();
        $metadata->tags = ["foo \n", " \nbar", " foo\n bar "];

        $this->assertEquals(['foo', 'bar', 'foo  bar'], $metadata->normalizedTags());
    }

    public function testNormalizedTagsCanDeduplication()
    {
        $metadata = new Metadata();
        $metadata->tags = ['foo ', ' foo', ' bar'];

        $this->assertEquals(['foo', 'bar'], $metadata->normalizedTags());
    }
}
