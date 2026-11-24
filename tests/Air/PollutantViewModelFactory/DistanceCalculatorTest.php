<?php declare(strict_types=1);

namespace App\Tests\Air\PollutantViewModelFactory;

use App\Air\ViewModelFactory\DistanceCalculator;
use App\Entity\Station;
use App\Geo\Coordinate\Coordinate;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function testDistance(): void
    {
        $coord = new Coordinate(53.11, 10.52);
        $station = new Station(57.55, 9.31);

        $this->assertEqualsWithDelta(499.58, DistanceCalculator::distance($coord, $station), 0.01);
    }
}