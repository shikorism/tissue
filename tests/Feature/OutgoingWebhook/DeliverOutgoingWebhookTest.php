<?php
declare(strict_types=1);

namespace Tests\Feature\OutgoingWebhook;

use App\Jobs\DeliverOutgoingWebhook;
use App\OutgoingWebhook;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliverOutgoingWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function makeJob(OutgoingWebhook $webhook, string $body = '{}', string $event = 'checkin.created', ?string $deliveryId = null): DeliverOutgoingWebhook
    {
        return new DeliverOutgoingWebhook($webhook, $event, $deliveryId ?? (string) Str::uuid(), $body);
    }

    public function testSuccessfulDeliveryRecordsSuccess()
    {
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id]);

        $mock = new MockHandler([new Response(200, [], 'ok')]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $deliveryId = (string) Str::uuid();
        $this->makeJob($webhook, deliveryId: $deliveryId)->handle($client);

        $this->assertDatabaseHas('outgoing_webhook_deliveries', [
            'outgoing_webhook_id' => $webhook->id,
            'user_id' => $user->id,
            'event' => 'checkin.created',
            'delivery_id' => $deliveryId,
            'is_success' => true,
            'status_code' => 200,
            'response_body' => 'ok',
        ]);
    }

    public function testServerErrorRecordsFailureAndRethrows()
    {
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id]);

        $mock = new MockHandler([new Response(500, [], 'Internal Server Error')]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        try {
            $this->makeJob($webhook)->handle($client);
            $this->fail('Expected RequestException was not thrown');
        } catch (RequestException) {
        }

        $this->assertDatabaseHas('outgoing_webhook_deliveries', [
            'outgoing_webhook_id' => $webhook->id,
            'is_success' => false,
            'status_code' => 500,
        ]);
    }

    public function testConnectionErrorRecordsFailureAndRethrows()
    {
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id]);

        $mock = new MockHandler([
            new ConnectException('Connection refused', new Request('POST', $webhook->url)),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        try {
            $this->makeJob($webhook)->handle($client);
            $this->fail('Expected ConnectException was not thrown');
        } catch (ConnectException) {
        }

        // Guzzle 7 では ConnectException は TransferException を継承（RequestException ではない）
        // → catch (\Throwable) に落ちるため response_body は汎用メッセージになる
        $this->assertDatabaseHas('outgoing_webhook_deliveries', [
            'outgoing_webhook_id' => $webhook->id,
            'is_success' => false,
            'status_code' => null,
            'response_body' => 'Delivery failed due to an internal server error',
        ]);
    }

    public function testResponseBodyIsTruncatedTo1024Chars()
    {
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id]);
        $longBody = str_repeat('a', 2000);

        $mock = new MockHandler([new Response(200, [], $longBody)]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->makeJob($webhook)->handle($client);

        $delivery = $webhook->deliveries()->first();
        $this->assertEquals(1024, strlen($delivery->response_body));
    }

    public function testSendsCorrectHeaders()
    {
        $user = User::factory()->create();
        $webhook = OutgoingWebhook::factory()->create(['user_id' => $user->id]);

        $capturedRequest = null;
        $mock = new MockHandler([new Response(200)]);
        $stack = HandlerStack::create($mock);
        $stack->push(function (callable $next) use (&$capturedRequest) {
            return function ($request, $options) use ($next, &$capturedRequest) {
                $capturedRequest = $request;

                return $next($request, $options);
            };
        });
        $client = new Client(['handler' => $stack]);

        $deliveryId = (string) Str::uuid();
        $this->makeJob($webhook, '{}', 'checkin.created', $deliveryId)->handle($client);

        $this->assertSame('application/json', $capturedRequest->getHeaderLine('Content-Type'));
        $this->assertSame('checkin.created', $capturedRequest->getHeaderLine('Tissue-Event'));
        $this->assertSame($deliveryId, $capturedRequest->getHeaderLine('Tissue-Delivery'));
        $this->assertStringContainsString('Tissue/', $capturedRequest->getHeaderLine('User-Agent'));
    }
}
