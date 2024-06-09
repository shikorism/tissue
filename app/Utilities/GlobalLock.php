<?php
declare(strict_types=1);

namespace App\Utilities;

use App\Exceptions\GlobalLockException;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * システムグローバルな排他ロックを取るためのユーティリティ
 */
class GlobalLock
{
    /**
     * ホスト名をキーとして排他ロックを取って処理を実行する
     * @param string $host ロック対象のホスト名
     * @param callable $fn ロックを獲得したら実行する処理
     * @return mixed `$fn` の戻り値
     * @throws GlobalLockException ロック・ロック解除処理に失敗した場合にスロー
     */
    public static function hostLock(string $host, callable $fn): mixed
    {
        return static::lock('host$' . $host, $fn);
    }

    /**
     * URIをキーとして排他ロックを取って処理を実行する
     * @param UriInterface $uri ロック対象のURI
     * @param callable $fn ロックを獲得したら実行する処理
     * @return mixed `$fn` の戻り値
     * @throws GlobalLockException ロック・ロック解除処理に失敗した場合にスロー
     */
    public static function uriLock(UriInterface $uri, callable $fn): mixed
    {
        $hash = hash('sha256', implode("\0", [$uri->getPath(), $uri->getQuery(), $uri->getFragment()]));

        return static::lock('uri$' . $uri->getHost() . '$' . $hash, $fn);
    }

    /**
     * URL文字列をキーとして排他ロックを取って処理を実行する
     * @param string $url ロック対象のURL文字列
     * @param callable $fn ロックを獲得したら実行する処理
     * @return mixed `$fn` の戻り値
     * @throws GlobalLockException ロック・ロック解除処理に失敗した場合にスロー
     */
    public static function urlLock(string $url, callable $fn): mixed
    {
        return static::uriLock(new Uri($url), $fn);
    }

    /**
     * 指定したキーで排他ロックを取って処理を実行する。
     * @param string $key ロックに使うキー
     * @param callable $fn ロックを獲得したら実行する処理
     * @return mixed `$fn` の戻り値
     * @throws GlobalLockException ロック・ロック解除処理に失敗した場合にスロー
     */
    public static function lock(string $key, callable $fn): mixed
    {
        $lockDir = storage_path('global_lock');
        if (!file_exists($lockDir)) {
            if (!mkdir($lockDir)) {
                throw new GlobalLockException("Lock failed! Can't create lock directory.");
            }
        }

        $lockFile = $lockDir . DIRECTORY_SEPARATOR . $key;
        $fp = fopen($lockFile, 'c+b');
        if ($fp === false) {
            throw new GlobalLockException("Lock failed! Can't open lock file.");
        }

        try {
            if (!flock($fp, LOCK_EX)) {
                throw new GlobalLockException("Lock failed! Can't lock file.");
            }

            try {
                $accessInfoText = stream_get_contents($fp);
                if ($accessInfoText !== false) {
                    $accessInfo = json_decode($accessInfoText, true);
                } else {
                    $accessInfo = [];
                }

                $result = $fn(isset($accessInfo['time']) ? new Carbon($accessInfo['time']) : null);

                $accessInfo = [
                    'time' => now()->toIso8601String()
                ];
                fseek($fp, 0);
                if (fwrite($fp, json_encode($accessInfo)) === false) {
                    throw new GlobalLockException("I/O Error! Can't write to lock file.");
                }

                return $result;
            } finally {
                if (!flock($fp, LOCK_UN)) {
                    throw new GlobalLockException("Unlock failed! Can't unlock file.");
                }
            }
        } finally {
            if (!fclose($fp)) {
                throw new GlobalLockException("Unlock failed! Can't close lock file.");
            }
        }

        throw new \LogicException('unreachable');
    }
}
