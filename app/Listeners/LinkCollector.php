<?php

namespace App\Listeners;

use App\Events\LinkDiscovered;
use App\Metadata;
use App\MetadataResolver\MetadataResolver;
use App\Utilities\Formatter;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LinkCollector
{
    /** @var Formatter */
    private $formatter;
    /** @var MetadataResolver */
    private $metadataResolver;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Formatter $formatter, MetadataResolver $metadataResolver)
    {
        $this->formatter = $formatter;
        $this->metadataResolver = $metadataResolver;
    }

    /**
     * Handle the event.
     *
     * @param  LinkDiscovered  $event
     * @return void
     */
    public function handle(LinkDiscovered $event)
    {
        // URLの正規化
        $url = $this->formatter->normalizeUrl($event->url);

        // 無かったら取得
        // TODO: ある程度古かったら再取得とかありだと思う
        $metadata = Metadata::find($url);
        if ($metadata == null) {
            $resolved = $this->metadataResolver->resolve($url);
            Metadata::create([
                'url' => $url,
                'title' => $resolved->title,
                'description' => $resolved->description,
                'image' => $resolved->image
            ]);
        }
    }
}
