<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\OutgoingWebhook>
 */
class OutgoingWebhookFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => 'Test Webhook',
            'url' => 'https://example.com/webhook',
            'is_active' => true,
            'on_checkin_created' => false,
            'on_checkin_updated' => false,
            'on_checkin_deleted' => false,
        ];
    }
}
