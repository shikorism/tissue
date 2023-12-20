<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CheckinWebhook extends Model
{
    use SoftDeletes, HasFactory;

    /** @var int ユーザーごとの作成数制限 */
    const PER_USER_LIMIT = 10;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (CheckinWebhook $webhook) {
            $webhook->id = Str::random(64);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAvailable()
    {
        return $this->user !== null;
    }
}
