<?php declare(strict_types=1);

namespace App\Tests\Air\AirQuality\PollutionLevel;

use App\Air\AirQuality\PollutionLevel\COLevel;
use App\Entity\Data;
use PHPUnit\Framework\TestCase;

class COPollutionLevelTest extends TestCase
{
    public function testIdentifier(): void
    {
        $this->assertEquals('co', (new COLevel())->getPollutionIdentifier());
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

        $this->assertEquals($levels, (new COLevel())->getLevels());
    }
}
