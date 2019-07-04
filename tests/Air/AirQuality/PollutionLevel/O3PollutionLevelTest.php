<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\O3Level;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class O3PollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('o3', (new O3Level())->getPollutionIdentifier());
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

        $this->assertEquals($levels, (new O3Level())->getLevels());
    }

    public function testLevels(): void
    {
        $this->assertEquals(0, (new O3Level())->getLevel((new Data())->setValue(250)));
        $this->assertEquals(1, (new O3Level())->getLevel((new Data())->setValue(3000)));
        $this->assertEquals(2, (new O3Level())->getLevel((new Data())->setValue(5000)));
        $this->assertEquals(3, (new O3Level())->getLevel((new Data())->setValue(12000)));
        $this->assertEquals(4, (new O3Level())->getLevel((new Data())->setValue(40000)));
    }
}
