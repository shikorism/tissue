<?php

namespace Tests\Feature\Api\Webhook;

use App\CheckinWebhook;
use App\Ejaculation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CheckinWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testSuccessful()
    {
        $user = factory(User::class)->create();
        $webhook = factory(CheckinWebhook::class)->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id, [
            'link' => 'http://example.com',
            'tags' => ['foo', 'bar']
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 200);

        $checkinId = $response->json('checkin.id');
        $ejaculation = Ejaculation::find($checkinId);
        $this->assertSame('http://example.com', $ejaculation->link);
        $this->assertCount(2, $ejaculation->tags);
        $this->assertSame(Ejaculation::SOURCE_WEBHOOK, $ejaculation->source);
        $this->assertNotEmpty($ejaculation->checkin_webhook_id);
    }

    public function testUserDestroyed()
    {
        $webhook = factory(CheckinWebhook::class)->create(['user_id' => null]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id);

        $response->assertStatus(404)
            ->assertJsonPath('status', 404)
            ->assertJsonPath('error.message', 'The webhook is unavailable');
    }

    public function testValidationFailed()
    {
        $user = factory(User::class)->create();
        $webhook = factory(CheckinWebhook::class)->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id, [
            'checked_in_at' => new Carbon('1999-12-31T23:59:00+0900'),
            'tags' => [
                'Has spaces'
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 422)
            ->assertJsonPath('error.message', 'Validation failed')
            ->assertJsonCount(2, 'error.violations');
    }

    public function testConflictCheckedInAt()
    {
        $user = factory(User::class)->create();
        $webhook = factory(CheckinWebhook::class)->create(['user_id' => $user->id]);
        $ejaculatedDate = new Carbon('2020-07-21T19:19:00+0900');
        factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => $ejaculatedDate
        ]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id, [
            'checked_in_at' => $ejaculatedDate,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 422)
            ->assertJsonPath('error.message', 'Checkin already exists in this time');
    }
}
