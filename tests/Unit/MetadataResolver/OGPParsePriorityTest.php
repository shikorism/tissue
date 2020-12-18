<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\OGPParsePriority;
use Tests\TestCase;

class OGPParsePriorityTest extends TestCase
{
    public function testPreferToOGP()
    {
        $prio = OGPParsePriority::preferTo(OGPParsePriority::OGP);

        $this->assertSame(
            ['//meta[@*="og:description"]', '//meta[@*="twitter:description"]', '//meta[@name="description"]'],
            $prio->sortForTitle('//meta[@*="og:description"]', '//meta[@*="twitter:description"]', '//meta[@name="description"]')
        );
        $this->assertSame(
            ['//meta[@*="og:description"]', '//meta[@*="twitter:description"]', '//meta[@name="description"]'],
            $prio->sortForTitle('//meta[@*="twitter:description"]', '//meta[@*="og:description"]', '//meta[@name="description"]')
        );
    }

    public function testPreferToTwitterCards()
    {
        $prio = OGPParsePriority::preferTo(OGPParsePriority::TWITTER_CARDS);

        $this->assertSame(
            ['//meta[@*="twitter:description"]', '//meta[@*="og:description"]', '//meta[@name="description"]'],
            $prio->sortForTitle('//meta[@*="og:description"]', '//meta[@*="twitter:description"]', '//meta[@name="description"]')
        );
        $this->assertSame(
            ['//meta[@*="twitter:description"]', '//meta[@*="og:description"]', '//meta[@name="description"]'],
            $prio->sortForTitle('//meta[@*="twitter:description"]', '//meta[@*="og:description"]', '//meta[@name="description"]')
        );
    }
}
