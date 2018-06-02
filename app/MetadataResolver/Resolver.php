<?php

namespace App\MetadataResolver;

interface Resolver
{
    public function resolve(string $url): Metadata;
}