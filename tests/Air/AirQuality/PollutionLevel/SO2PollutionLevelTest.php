<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\SO2Level;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class SO2PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('so2', (new SO2Level())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            25,
            50,
            120,
            350,
            1000,
        ];

        $this->assertEquals($levels, (new SO2Level())->getLevels());
    }
}
