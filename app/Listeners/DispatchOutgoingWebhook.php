<?php
declare(strict_types=1);

namespace App\Listeners;

use App\Events\CheckinCreated;
use App\Events\CheckinDeleted;
use App\Events\CheckinUpdated;
use App\Http\Resources\EjaculationResource;
use App\Jobs\DeliverOutgoingWebhook;
use App\OutgoingWebhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Str;

class DispatchOutgoingWebhook
{
    public function handle(CheckinCreated|CheckinUpdated|CheckinDeleted $event): void
    {
        $eventName = match ($event::class) {
            CheckinCreated::class => 'checkin.created',
            CheckinUpdated::class => 'checkin.updated',
            CheckinDeleted::class => 'checkin.deleted',
        };
        $subscriptionColumn = match ($event::class) {
            CheckinCreated::class => 'on_checkin_created',
            CheckinUpdated::class => 'on_checkin_updated',
            CheckinDeleted::class => 'on_checkin_deleted',
        };

        $webhooks = $event->ejaculation->user->outgoingWebhooks
            ->where('is_active', true)
            ->where($subscriptionColumn, true);
        if ($webhooks->isEmpty()) {
            return;
        }

        $deliveryId = Str::uuid()->toString();
        $template = [
            'event' => $eventName,
            'delivery_id' => $deliveryId,
            'webhook_id' => null,
            'triggered_at' => now()->toIso8601String(),
            'payload' => (new EjaculationResource($event->ejaculation))->jsonSerialize(),
        ];

        foreach ($webhooks as $webhook) {
            $body = $template;
            $body['webhook_id'] = $webhook->id;

            DeliverOutgoingWebhook::dispatch(
                $webhook,
                $eventName,
                $deliveryId,
                json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }
    }
}
