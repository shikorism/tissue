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
        Carbon::setTestNow('2020-07-21 19:19:19');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testSuccessful()
    {
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id, [
            'checked_in_at' => Carbon::create(2019, 7, 21, 19, 19, 19)->toIso8601String(),
            'note' => 'test test test',
            'link' => 'http://example.com',
            'tags' => ['foo', 'bar'],
            'is_private' => false,
            'is_too_sensitive' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 200);

        $checkinId = $response->json('checkin.id');
        $ejaculation = Ejaculation::find($checkinId);
        $this->assertEquals(Carbon::create(2019, 7, 21, 19, 19, 0), $ejaculation->ejaculated_date);
        $this->assertSame('test test test', $ejaculation->note);
        $this->assertSame('http://example.com', $ejaculation->link);
        $this->assertCount(2, $ejaculation->tags);
        $this->assertFalse($ejaculation->is_private);
        $this->assertFalse($ejaculation->is_too_sensitive);
        $this->assertSame(Ejaculation::SOURCE_WEBHOOK, $ejaculation->source);
        $this->assertNotEmpty($ejaculation->checkin_webhook_id);
    }

    public function testSuccessfulPrivateAndSensitive()
    {
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id, [
            'is_private' => true,
            'is_too_sensitive' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 200);

        $checkinId = $response->json('checkin.id');
        $ejaculation = Ejaculation::find($checkinId);
        $this->assertTrue($ejaculation->is_private);
        $this->assertTrue($ejaculation->is_too_sensitive);
        $this->assertSame(Ejaculation::SOURCE_WEBHOOK, $ejaculation->source);
        $this->assertNotEmpty($ejaculation->checkin_webhook_id);
    }

    public function testSuccessfulAllDefault()
    {
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 200);

        $checkinId = $response->json('checkin.id');
        $ejaculation = Ejaculation::find($checkinId);
        $this->assertEquals(Carbon::create(2020, 7, 21, 19, 19, 0), $ejaculation->ejaculated_date);
        $this->assertEmpty($ejaculation->note);
        $this->assertEmpty($ejaculation->link);
        $this->assertEmpty($ejaculation->tags);
        $this->assertFalse($ejaculation->is_private);
        $this->assertFalse($ejaculation->is_too_sensitive);
        $this->assertSame(Ejaculation::SOURCE_WEBHOOK, $ejaculation->source);
        $this->assertNotEmpty($ejaculation->checkin_webhook_id);
    }

    public function testUserDestroyed()
    {
        $webhook = CheckinWebhook::factory()->create(['user_id' => null]);

        $response = $this->postJson('/api/webhooks/checkin/' . $webhook->id);

        $response->assertStatus(404)
            ->assertJsonPath('status', 404)
            ->assertJsonPath('error.message', 'The webhook is unavailable');
    }

    public function testValidationFailed()
    {
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);

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
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);
        $ejaculatedDate = new Carbon('2020-07-21T19:19:00+0900');
        Ejaculation::factory()->create([
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
