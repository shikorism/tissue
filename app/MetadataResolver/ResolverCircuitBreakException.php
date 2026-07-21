<?php

namespace App\MetadataResolver;

use Throwable;

/**
 * 規定回数以上の解決失敗により、メタデータの取得が不能となっている場合にスローされます。
 */
class ResolverCircuitBreakException extends \RuntimeException
{
    public function __construct(int $errorCount, string $url, ?Throwable $previous = null)
    {
        parent::__construct("{$errorCount}回失敗しているためメタデータの取得を中断しました: {$url}", 0, $previous);
    }
}
