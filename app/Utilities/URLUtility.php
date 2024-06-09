<?php
declare(strict_types=1);

namespace App\Utilities;

class URLUtility
{
    /**
     * URLからホスト部とポート部を抽出
     * @param string $url
     * @return string
     */
    public static function getHostWithPortFromUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = $parts['host'];
        if (isset($parts['port'])) {
            $host .= ':' . $parts['port'];
        }

        return $host;
    }
}
