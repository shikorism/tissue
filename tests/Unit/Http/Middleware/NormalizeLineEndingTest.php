<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\NormalizeLineEnding;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class NormalizeLineEndingTest extends TestCase
{
    public function testCRLFtoLF()
    {
        $request = Request::create('/');
        $request->replace([
            'test' => "foo\r\nbar"
        ]);

        $middleware = new NormalizeLineEnding();

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals("foo\nbar", $request->input('test'));
        });
    }

    public function testCRtoLF()
    {
        $request = Request::create('/');
        $request->replace([
            'test' => "foo\rbar"
        ]);

        $middleware = new NormalizeLineEnding();

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals("foo\nbar", $request->input('test'));
        });
    }

    public function testLFtoLF()
    {
        $request = Request::create('/');
        $request->replace([
            'test' => "foo\nbar"
        ]);

        $middleware = new NormalizeLineEnding();

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals("foo\nbar", $request->input('test'));
        });
    }

    public function testArrayRequest()
    {
        $request = Request::create('/');
        $request->replace([
            'test' => "foo\r\nbar",
            'hash' => [
                'yuzuki' => "yuzuki\r\nyukari",
                'miku' => "hatsune\r\nmiku",
            ],
            'array' => [
                "kagamine\r\nrin",
                "kagamine\r\nlen"
            ]
        ]);

        $middleware = new NormalizeLineEnding();

        $middleware->handle($request, function (Request $request) {
            $this->assertEquals("foo\nbar", $request->input('test'));
            $this->assertEquals("yuzuki\nyukari", $request->input('hash.yuzuki'));
            $this->assertEquals("hatsune\nmiku", $request->input('hash.miku'));
            $this->assertEquals("kagamine\nrin", $request->input('array.0'));
            $this->assertEquals("kagamine\nlen", $request->input('array.1'));
        });
    }
}
