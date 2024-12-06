<?php

namespace App;

use Carbon\CarbonInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'url';
    protected $keyType = 'string';

    protected $fillable = ['url', 'title', 'description', 'image', 'expires_at'];
    protected $visible = ['url', 'title', 'description', 'image', 'expires_at', 'tags'];

    protected $casts = [
        'expires_at' => 'datetime',
        'error_at' => 'datetime',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function needRefresh(): bool
    {
        return $this->isExpired() || $this->error_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at < now();
    }

    public function storeException(CarbonInterface $error_at, \Exception $exception): self
    {
        $this->prepareFieldsOnError();
        $this->error_at = $error_at;
        $this->error_exception_class = get_class($exception);
        $this->error_body = $exception->getMessage();
        if ($exception instanceof RequestException) {
            $this->error_http_code = $exception->getCode();
        } else {
            $this->error_http_code = null;
        }
        $this->error_count++;

        return $this;
    }

    public function storeError(CarbonInterface $error_at, string $body, ?int $httpCode = null): self
    {
        $this->prepareFieldsOnError();
        $this->error_at = $error_at;
        $this->error_exception_class = null;
        $this->error_body = $body;
        $this->error_http_code = $httpCode;
        $this->error_count++;

        return $this;
    }

    public function clearError(): self
    {
        $this->error_at = null;
        $this->error_exception_class = null;
        $this->error_body = null;
        $this->error_http_code = null;
        $this->error_count = 0;

        return $this;
    }

    private function prepareFieldsOnError()
    {
        $this->title = $this->title ?? '';
        $this->description = $this->description ?? '';
        $this->image = $this->image ?? '';
    }
}
