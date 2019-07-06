<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\PM10Level;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class PM10PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('pm10', (new PM10Level())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            10,
            20,
            35,
            50,
            100,
        ];

        $this->assertEquals($levels, (new PM10Level())->getLevels());
    }
}
