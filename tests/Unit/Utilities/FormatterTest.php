<?php

namespace Tests\Unit\Utilities;

use App\Utilities\Formatter;
use Tests\TestCase;

class FormatterTest extends TestCase
{
    public function testFormatIntervalHundredDays()
    {
        $formatter = new Formatter();
        $this->assertSame('100日 0時間 0分', $formatter->formatInterval(100 * 86400));
    }

    public function testFormatIntervalThousandDays()
    {
        $formatter = new Formatter();
        $this->assertSame('1,000日 0時間 0分', $formatter->formatInterval(1000 * 86400));
    }

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

    public function testProfileImageSrcSet()
    {
        $formatter = new Formatter();
        $profileImageProvider = new class() {
            public function getProfileImageUrl(int $size)
            {
                return "https://example.com/$size.png";
            }
        };

        $this->assertSame(
            'https://example.com/128.png 1x,https://example.com/256.png 2x',
            $formatter->profileImageSrcSet($profileImageProvider, 128, 2)
        );
    }

    /**
     * @dataProvider provideNormalizeTagName
     */
    public function testNormalizeTagName($input, $expected)
    {
        $formatter = new Formatter();

        $normalized = $formatter->normalizeTagName($input);
        $this->assertSame($expected, $normalized);
        $this->assertSame($expected, $formatter->normalizeTagName($normalized));
    }

    public function provideNormalizeTagName()
    {
        return [
            'LowerCase' => ['example', 'example'],
            'UpperCase' => ['EXAMPLE', 'example'],
            'HalfWidthKana' => ['ﾃｨｯｼｭ', 'ティッシュ'],
            'FullWidthAlphabet' => ['Ｔｉｓｓｕｅ', 'tissue'],
            '組み文字1' => ['13㎝', '13cm'],
            '組み文字2' => ['13㌢㍍', '13センチメートル'],
            'Script' => ['ℬ𝒶𝒷𝓊𝓂𝒾', 'babumi'],
            'NFD' => ['オカズ', 'オカズ'],
        ];
    }
}
