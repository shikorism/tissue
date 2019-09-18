<?php

namespace Tests;

trait MyAsserts
{
    /**
     * assertArraySubset()がdeprecatedって本当ですか？ 配列の中に所定の値が全て含まれていることを検証します。
     * @param array $expected
     * @param array $actual
     * @param string $message
     */
    public function assertArrayContains(array $expected, array $actual, string $message = '')
    {
        $this->assertSame($expected, array_intersect($actual, $expected), $message);
    }
}
