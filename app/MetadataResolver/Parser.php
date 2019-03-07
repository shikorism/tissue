<?php

namespace App\MetadataResolver;

interface Parser
{
    public function parse(string $body): Metadata;
}
