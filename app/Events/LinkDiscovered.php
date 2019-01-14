<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LinkDiscovered
{
    use Dispatchable, SerializesModels;

    public $url;

    /**
     * Create a new event instance.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
