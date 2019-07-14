<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\PM25Level;
use PHPUnit\Framework\TestCase;

class PM25PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('pm25', (new PM25Level())->getPollutionIdentifier());
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

        $this->assertEquals($levels, (new PM25Level())->getLevels());
    }
}
