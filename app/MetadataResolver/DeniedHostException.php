<?php

namespace App\MetadataResolver;

use Exception;
use Throwable;

/**
 * メタデータの解決を禁止しているホストに対して取得を試み、ブロックされたことを表します。
 */
class DeniedHostException extends Exception
{
    private $url;

    public function __construct(string $url, ?Throwable $previous = null)
    {
        parent::__construct("Access denied by system policy: $url", 0, $previous);
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHost(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }
}
