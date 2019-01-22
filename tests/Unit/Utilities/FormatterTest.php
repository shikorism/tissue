<?php

namespace Tests\Unit\Utilities;

use App\Utilities\Formatter;
use Tests\TestCase;

class FormatterTest extends TestCase
{
    public function testNormalizeUrlWithoutQuery()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to';
        $this->assertEquals($url, $formatter->normalizeUrl($url));
    }

    public function testNormalizeUrlWithSortedQuery()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to?foo=bar&hoge=fuga';
        $this->assertEquals($url, $formatter->normalizeUrl($url));
    }

    public function testNormalizeUrlWithUnsortedQuery()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to?hoge=fuga&foo=bar';
        $this->assertEquals('http://example.com/path/to?foo=bar&hoge=fuga', $formatter->normalizeUrl($url));
    }

    public function testNormalizeUrlWithSortedQueryAndFragment()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to?foo=bar&hoge=fuga#fragment';
        $this->assertEquals($url, $formatter->normalizeUrl($url));
    }

    public function testNormalizeUrlWithFragment()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to#fragment';
        $this->assertEquals($url, $formatter->normalizeUrl($url));
    }

    public function testNormalizeUrlWithSortedQueryAndZeroLengthFragment()
    {
        $formatter = new Formatter();

        $url = 'http://example.com/path/to?foo=bar&hoge=fuga#';
        $this->assertEquals('http://example.com/path/to?foo=bar&hoge=fuga', $formatter->normalizeUrl($url));
    }
}
