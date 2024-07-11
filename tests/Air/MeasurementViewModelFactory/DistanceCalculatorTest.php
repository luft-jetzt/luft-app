<?php declare(strict_types=1);

namespace App\Tests\Air\MeasurementViewModelFactory;

use App\Air\ViewModelFactory\DistanceCalculator;
use App\Entity\Station;
use Caldera\GeoBasic\Coordinate\Coordinate;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function testDistance(): void
    {
        $coord = new Coordinate(53.11, 10.52);
        $station = new Station(57.55, 9.31);

        $this->assertEquals(499.58, DistanceCalculator::distance($coord, $station));
    }
}