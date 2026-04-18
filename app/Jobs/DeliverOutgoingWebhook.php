<?php
declare(strict_types=1);

namespace App\Jobs;

use App\OutgoingWebhook;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class DeliverOutgoingWebhook implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 4;
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly OutgoingWebhook $webhook,
        private readonly string $eventName,
        private readonly string $deliveryId,
        private readonly string $body
    ) {
    }

    public function backoff(): array
    {
        return [60, 300, 1800];
    }

    /**
     * Execute the job.
     */
    public function handle(Client $client): void
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'User-Agent' => 'Tissue/1.0 (webhook; +https://github.com/shikorism/tissue)',
                'Tissue-Event' => $this->eventName,
                'Tissue-Delivery' => $this->deliveryId,
            ];

            $response = $client->post($this->webhook->url, [
                'headers' => $headers,
                'body' => $this->body,
                'timeout' => 30,
                'http_errors' => false,
            ]);
            $isSuccess = 200 <= $response->getStatusCode() && $response->getStatusCode() <= 299;
            $this->recordDelivery(
                $isSuccess,
                $response->getStatusCode(),
                substr($response->getBody()->getContents(), 0, 1024),
            );

            if (!$isSuccess) {
                $this->fail("Delivery failed with status code {$response->getStatusCode()}");
            }
        } catch (\Throwable $e) {
            $this->recordDelivery(false, null, 'Delivery failed due to an internal server error');
            throw $e;
        }
    }

    private function recordDelivery(bool $isSuccess, ?int $statusCode, ?string $responseBody): void
    {
        $this->webhook->deliveries()->create([
            'user_id' => $this->webhook->user_id,
            'delivery_id' => $this->deliveryId,
            'event' => $this->eventName,
            'is_success' => $isSuccess,
            'status_code' => $statusCode,
            'request_body' => $this->body,
            'response_body' => $responseBody,
            'delivered_at' => now(),
        ]);
    }
}
