<?php

namespace App\Parser\SearchQuery;

/**
 * クエリを検証した結果、不正な条件が与えられている場合にスローされます。
 */
class InvalidExpressionException extends \RuntimeException
{
}
