<?php

namespace App\MetadataResolver;

use RuntimeException;
use Throwable;

/**
 * ContentProviderの提供するrobots.txtによってクロールが拒否された場合にスローされます。
 */
class DisallowedByProviderException extends RuntimeException
{
    private $url;

    public function __construct(string $url, Throwable $previous = null)
    {
        parent::__construct("Access denied by robots.txt: $url", 0, $previous);
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
