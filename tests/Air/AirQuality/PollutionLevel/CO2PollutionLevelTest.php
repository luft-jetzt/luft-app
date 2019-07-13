<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\CO2Level;
use PHPUnit\Framework\TestCase;

class CO2PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('co2', (new CO2Level())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            3 => 300,
            4 => 350,
        ];

        $this->assertEquals($levels, (new CO2Level())->getLevels());
    }
}
