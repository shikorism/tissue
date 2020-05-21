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
        return $this->linkify->processUrls($text, ['attr' => ['target' => '_blank', 'rel' => 'noopener']]);
    }

    /**
     * URLを正規化します。
     * @param string $url URL
     * @return string 正規化されたURL
     */
    public function normalizeUrl($url)
    {
        // Decode
        $url = urldecode($url);

        // Remove Hashbang
        $url = preg_replace('~/#!/~u', '/', $url);

        // Sort query parameters
        $parts = parse_url($url);
        if (!empty($parts['query'])) {
            // Remove query parameters
            $url = str_replace_last('?' . $parts['query'], '', $url);
            if (!empty($parts['fragment'])) {
                // Remove fragment identifier
                $url = str_replace_last('#' . $parts['fragment'], '', $url);
            } else {
                // "http://example.com/?query#" の場合 $parts['fragment'] は unset になるので、個別に判定して除去する必要がある
                $url = preg_replace('/#\z/u', '', $url);
            }

            parse_str($parts['query'], $params);
            ksort($params);

            $url = $url . '?' . http_build_query($params);
            if (!empty($parts['fragment'])) {
                $url .= '#' . $parts['fragment'];
            }
        }

        return $url;
    }

    /**
     * imgタグのsrcsetで使用できる形式で、プロフィール画像URLを生成します。
     * @param object $user Userなど、getProfileImageUrl()が実装されているオブジェクト
     * @param int $baseSize 1x解像度における画像サイズ
     * @param int $maxDensity 最高密度
     * @return string srcset用の文字列
     */
    public function profileImageSrcSet($user, int $baseSize, int $maxDensity = 3)
    {
        $srcset = [];
        for ($i = 1; $i <= $maxDensity; $i++) {
            $srcset[] = $user->getProfileImageUrl($baseSize * $i) . " {$i}x";
        }

        return implode(',', $srcset);
    }

    /**
     * php.ini書式のデータサイズを正規化します。
     * @param mixed $val データサイズ
     * @return string
     */
    public function normalizeIniBytes($val)
    {
        $val = trim($val);
        $last = strtolower(substr($val, -1, 1));
        if (ord($last) < 0x30 || ord($last) > 0x39) {
            $bytes = substr($val, 0, -1);
            switch ($last) {
                case 'g':
                    $bytes *= 1024;
                    // fall through
                    // no break
                case 'm':
                    $bytes *= 1024;
                    // fall through
                    // no break
                case 'k':
                    $bytes *= 1024;
                    break;
            }
        } else {
            $bytes = $val;
        }

        if ($bytes >= (1 << 30)) {
            return ($bytes >> 30) . 'GB';
        } elseif ($bytes >= (1 << 20)) {
            return ($bytes >> 20) . 'MB';
        } elseif ($bytes >= (1 << 10)) {
            return ($bytes >> 10) . 'KB';
        }

        return $bytes . 'B';
    }
}
