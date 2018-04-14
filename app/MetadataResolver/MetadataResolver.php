<?php

namespace App\MetadataResolver;

class MetadataResolver implements Resolver
{
    public $rules = [
        '~(((sp\.)?seiga\.nicovideo\.jp/seiga(/#!)?|nico\.ms))/im~' => NicoSeigaResolver::class,
        '~nijie\.info/view\.php~' => NijieResolver::class,
        '/.*/' => OGPResolver::class
    ];

    public function resolve(string $url): Metadata
    {
        foreach ($this->rules as $pattern => $class) {
            if (preg_match($pattern, $url) === 1) {
                $resolver = new $class;
                return $resolver->resolve($url);
            }
        }

        throw new \UnexpectedValueException('URL not matched.');
    }
}