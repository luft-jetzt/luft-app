<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\UVIndexLevel;
use PHPUnit\Framework\TestCase;

class UVIndexPollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('uvindex', (new UVIndexLevel())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            2,
            5,
            7,
            11,
        ];

        $this->assertEquals($levels, (new UVIndexLevel())->getLevels());
    }
}
