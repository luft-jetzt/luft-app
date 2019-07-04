<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\NO2Level;
use App\Entity\Data;
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
            0 => 1000,
            1 => 2000,
            2 => 4000,
            3 => 10000,
            4 => 30000,
        ];

        $this->assertEquals($levels, (new NO2Level())->getLevels());
    }

    public function testLevels(): void
    {
        $this->assertEquals(0, (new NO2Level())->getLevel((new Data())->setValue(250)));
        $this->assertEquals(1, (new NO2Level())->getLevel((new Data())->setValue(3000)));
        $this->assertEquals(2, (new NO2Level())->getLevel((new Data())->setValue(5000)));
        $this->assertEquals(3, (new NO2Level())->getLevel((new Data())->setValue(12000)));
        $this->assertEquals(4, (new NO2Level())->getLevel((new Data())->setValue(40000)));
    }
}
