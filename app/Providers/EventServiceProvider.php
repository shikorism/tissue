<?php

namespace App\Providers;

use App\Events\CheckinCreated;
use App\Events\CheckinDeleted;
use App\Events\CheckinUpdated;
use App\Listeners\DispatchOutgoingWebhook;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\LinkDiscovered' => [
            'App\Listeners\LinkCollector'
        ],
        CheckinCreated::class => [
            DispatchOutgoingWebhook::class,
        ],
        CheckinUpdated::class => [
            DispatchOutgoingWebhook::class,
        ],
        CheckinDeleted::class => [
            DispatchOutgoingWebhook::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
