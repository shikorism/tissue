<?php

namespace Tests;

use App\Services\MetadataResolveService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->when(MetadataResolveService::class)->needs('$circuitBreakCount')->give(5);
    }
}
