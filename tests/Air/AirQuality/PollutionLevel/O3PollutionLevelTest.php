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
            33,
            65,
            120,
            180,
            240,
        ];

        $this->assertEquals($levels, (new O3Level())->getLevels());
    }
}
