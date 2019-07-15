<?php declare(strict_types=1);

namespace App\Tests\Air\MeasurementViewModelFactory;

use App\Air\ViewModelFactory\DistanceCalculator;
use App\Entity\Station;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function testDistance(): void
    {
        $coord = new Coord(53.11, 10.52);
        $station = new Station(57.55, 9.31);

        $this->assertEquals(500.14081935242, DistanceCalculator::distance($coord, $station));
    }
}