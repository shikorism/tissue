<?php

namespace App\Http\Controllers\Api;


use App\Metadata;
use App\MetadataResolver\MetadataResolver;
use App\Utilities\Formatter;
use Illuminate\Http\Request;

class CardController
{
    /**
     * @var MetadataResolver
     */
    private $resolver;
    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(MetadataResolver $resolver, Formatter $formatter)
    {
        $this->resolver = $resolver;
        $this->formatter = $formatter;
    }

    public function show(Request $request)
    {
        $request->validate([
            'url:required|url'
        ]);

        $url = $this->formatter->normalizeUrl($request->input('url'));

        $metadata = Metadata::find($url);
        if ($metadata === null || ($metadata->expires_at !== null && $metadata->expires_at < now())) {
            $resolved = $this->resolver->resolve($url);
            $metadata = Metadata::updateOrCreate(['url' => $url], [
                'title' => $resolved->title,
                'description' => $resolved->description,
                'image' => $resolved->image,
                'expires_at' => $resolved->expires_at
            ]);
        }

        $response = response($metadata);
        if (!config('app.debug')) {
            $response = $response->setCache(['public' => true, 'max_age' => 86400]);
        }

        return $response;
    }
}
