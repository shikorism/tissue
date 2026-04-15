<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutgoingWebhook extends Model
{
    /** @var int ユーザーごとの作成数制限 */
    const PER_USER_LIMIT = 3;

    protected $fillable = [
        'name',
        'is_active',
        'on_checkin_created',
        'on_checkin_updated',
        'on_checkin_deleted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries()
    {
        return $this->hasMany(OutgoingWebhookDelivery::class);
    }
}
