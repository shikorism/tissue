<?php
declare(strict_types=1);

namespace App\Exceptions;

use App\Utilities\GlobalLock;

/**
 * {@see GlobalLock} のロック処理に失敗した時にスローされる例外
 */
class GlobalLockException extends \RuntimeException
{
}
