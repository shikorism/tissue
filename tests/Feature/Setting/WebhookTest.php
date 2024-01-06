<?php

namespace Tests\Feature\Setting;

use App\CheckinWebhook;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testStoreWebhooks()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/setting/webhooks', ['name' => 'example']);

        $response->assertStatus(200)
            ->assertViewIs('setting.webhooks');
        $this->assertDatabaseHas('checkin_webhooks', ['user_id' => $user->id, 'name' => 'example']);
    }

    public function testStoreWebhooksHas9Hooks()
    {
        $user = User::factory()->create();
        $webhooks = CheckinWebhook::factory(CheckinWebhook::PER_USER_LIMIT - 1)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/setting/webhooks', ['name' => 'example9']);

        $response->assertStatus(200)
            ->assertViewIs('setting.webhooks');
        $this->assertDatabaseHas('checkin_webhooks', ['user_id' => $user->id, 'name' => 'example9']);
    }

    public function testStoreWebhooksHas10Hooks()
    {
        $user = User::factory()->create();
        $webhooks = CheckinWebhook::factory(CheckinWebhook::PER_USER_LIMIT)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/setting/webhooks', ['name' => 'example10']);

        $response->assertStatus(200)
            ->assertViewIs('setting.webhooks');
        $this->assertDatabaseMissing('checkin_webhooks', ['user_id' => $user->id, 'name' => 'example10']);
    }

    public function testDestroyWebhooks()
    {
        $user = User::factory()->create();
        $webhook = CheckinWebhook::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->delete('/setting/webhooks/' . $webhook->id);

        $response->assertStatus(200)
            ->assertViewIs('setting.webhooks')
            ->assertSee('削除しました');
        $this->assertTrue($webhook->refresh()->trashed());
    }
}
