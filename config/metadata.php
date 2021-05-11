<?php

return [
    'circuit_break_count' => env('METADATA_RESOLVER_CIRCUIT_BREAK_COUNT', 5),
    'ignore_access_interval' => (bool) env('METADATA_RESOLVER_IGNORE_ACCESS_INTERVAL', false),
    'no_cache' => (bool) env('METADATA_NO_CACHE', false),
];
