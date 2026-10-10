<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\UVIndexMaxLevel;
use PHPUnit\Framework\TestCase;

class UVIndexMaxLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertSame('uvindex_max', (new UVIndexMaxLevel())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            2,
            5,
            8,
            11,
        ];

        $this->assertSame($levels, (new UVIndexMaxLevel())->getLevels());
    }
}
