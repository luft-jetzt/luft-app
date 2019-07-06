<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\NO2Level;
use PHPUnit\Framework\TestCase;

class NO2PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('no2', (new NO2Level())->getPollutionIdentifier());
    }

    public function testLevelList(): void
    {
        $levels = [
            25,
            50,
            100,
            200,
            500,
        ];

        $this->assertEquals($levels, (new NO2Level())->getLevels());
    }
}
