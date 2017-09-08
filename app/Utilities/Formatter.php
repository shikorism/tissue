<?php

namespace App\Utilities;

class Formatter
{
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
}