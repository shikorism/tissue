<?php
declare(strict_types=1);

namespace Tests\Feature\OutgoingWebhook;

use App\Ejaculation;
use App\Events\CheckinCreated;
use App\Events\CheckinDeleted;
use App\Events\CheckinUpdated;
use App\Jobs\DeliverOutgoingWebhook;
use App\OutgoingWebhook;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DispatchOutgoingWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testDispatchesJobOnCheckinCreated()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->create(['user_id' => $user->id, 'on_checkin_created' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinCreated::dispatch($ejaculation);

        Bus::assertDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDispatchesJobOnCheckinUpdated()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->create(['user_id' => $user->id, 'on_checkin_updated' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinUpdated::dispatch($ejaculation);

        Bus::assertDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDispatchesJobOnCheckinDeleted()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->create(['user_id' => $user->id, 'on_checkin_deleted' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinDeleted::dispatch($ejaculation);

        Bus::assertDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDoesNotDispatchWhenFlagDisabled()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->create(['user_id' => $user->id, 'on_checkin_created' => false]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinCreated::dispatch($ejaculation);

        Bus::assertNotDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDoesNotDispatchWhenWebhookIsInactive()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->create([
            'user_id' => $user->id,
            'is_active' => false,
            'on_checkin_created' => true,
        ]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinCreated::dispatch($ejaculation);

        Bus::assertNotDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDoesNotDispatchForOtherUsersWebhook()
    {
        Bus::fake();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        OutgoingWebhook::factory()->create(['user_id' => $otherUser->id, 'on_checkin_created' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinCreated::dispatch($ejaculation);

        Bus::assertNotDispatched(DeliverOutgoingWebhook::class);
    }

    public function testDispatchesOneJobPerWebhook()
    {
        Bus::fake();
        $user = User::factory()->create();
        OutgoingWebhook::factory()->count(2)->create(['user_id' => $user->id, 'on_checkin_created' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        CheckinCreated::dispatch($ejaculation);

        Bus::assertDispatchedTimes(DeliverOutgoingWebhook::class, 2);
    }

    public function testSharedDeliveryIdAcrossMultipleWebhooks()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create();
        OutgoingWebhook::factory()->count(2)->create(['user_id' => $user->id, 'on_checkin_created' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        $mock = new MockHandler([new Response(200), new Response(200)]);
        $this->app->bind(Client::class, fn () => new Client(['handler' => HandlerStack::create($mock)]));

        CheckinCreated::dispatch($ejaculation);

        $deliveries = \App\OutgoingWebhookDelivery::all();
        $this->assertCount(2, $deliveries);
        $this->assertEquals(1, $deliveries->pluck('delivery_id')->unique()->count());
    }

    public function testCheckinDeletedPayloadContainsIdOnly()
    {
        config(['queue.default' => 'sync']);
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id, 'on_checkin_deleted' => true]);
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);

        $mock = new MockHandler([new Response(200)]);
        $this->app->bind(Client::class, fn () => new Client(['handler' => HandlerStack::create($mock)]));

        CheckinDeleted::dispatch($ejaculation);

        $body = json_decode($webhook->deliveries()->first()->request_body, true);
        $this->assertEquals(['id' => $ejaculation->id], $body['payload']);
        $this->assertSame('checkin.deleted', $body['event']);
    }
}
