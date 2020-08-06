<?php

namespace App\Listeners;

use App\Events\LinkDiscovered;
use App\MetadataResolver\DeniedHostException;
use App\Services\MetadataResolveService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LinkCollector
{
    /** @var MetadataResolveService */
    private $metadataResolveService;

    /**
     * Create the event listener.
     *
     * @param MetadataResolveService $metadataResolveService
     */
    public function __construct(MetadataResolveService $metadataResolveService)
    {
        $this->metadataResolveService = $metadataResolveService;
    }

    /**
     * Handle the event.
     *
     * @param  LinkDiscovered  $event
     * @return void
     */
    public function handle(LinkDiscovered $event)
    {
        try {
            $this->metadataResolveService->execute($event->url);
        } catch (DeniedHostException $e) {
            // ignored
        } catch (\Exception $e) {
            // 今のところこのイベントは同期実行されるので、上流をクラッシュさせないために雑catchする
            report($e);
        }
    }
}
