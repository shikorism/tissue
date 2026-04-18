<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutgoingWebhookDelivery extends Model
{
    protected $fillable = [
        'outgoing_webhook_id',
        'user_id',
        'delivery_id',
        'event',
        'is_success',
        'status_code',
        'request_body',
        'response_body',
        'delivered_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function outgoingWebhook()
    {
        return $this->belongsTo(OutgoingWebhook::class);
    }
}
