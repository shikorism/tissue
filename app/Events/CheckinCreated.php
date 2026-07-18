<?php
declare(strict_types=1);

namespace App\Events;

use App\Ejaculation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckinCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Ejaculation $ejaculation)
    {
    }
}
