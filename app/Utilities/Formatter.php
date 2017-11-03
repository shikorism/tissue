<?php

namespace App\Utilities;

use Misd\Linkify\Linkify;

class Formatter
{
    /** @var Linkify */
    private $linkify;

    public function __construct()
    {
        $this->linkify = new Linkify();
    }

    /**
     * 通算秒数を日数と時分にフォーマットします。
     * @param int|float $value 通算秒数
     * @return string "xx日 xx時間 xx分" 形式でフォーマットされた文字列
     */
    public function formatInterval($value)
    {
        $days = floor($value / 86400);
        $hours = floor($value % 86400 / 3600);
        $minutes = floor($value % 3600 / 60);
        return "{$days}日 {$hours}時間 {$minutes}分";
    }

    /**
     * テキスト内のURLをHTMLのリンクに置き換えます。
     * @param string $text テキスト
     * @return string URLをリンクに置き換えた文字列
     */
    public function linkify($text)
    {
        return $this->linkify->processUrls($text);
    }
}