<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\TemperatureLevel;
use PHPUnit\Framework\TestCase;

class TemperaturePollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('temperature', (new TemperatureLevel())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            0 => 23,
            1 => 28,
            2 => 33,
            3 => 38,
        ];

        $this->assertEquals($levels, (new TemperatureLevel())->getLevels());
    }
}
