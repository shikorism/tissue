<?php
declare(strict_types=1);

namespace Tests\Unit\Utilities;

use App\Utilities\URLUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class URLUtilityTest extends TestCase
{
    #[DataProvider('provideGetHostWithPortFromUrl')]
    public function testGetHostWithPortFromUrl($expected, $url)
    {
        $this->assertSame($expected, URLUtility::getHostWithPortFromUrl($url));
    }

    public static function provideGetHostWithPortFromUrl()
    {
        return [
            'host' => ['example.com', 'http://example.com'],
            'host with port' => ['example.com:8080', 'http://example.com:8080'],
        ];
    }
}
