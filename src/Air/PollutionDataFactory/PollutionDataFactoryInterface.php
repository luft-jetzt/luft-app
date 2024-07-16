<?php declare(strict_types=1);

namespace App\Air\PollutionDataFactory;

use App\Air\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use App\Entity\Station;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

interface PollutionDataFactoryInterface
{
    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null, int $workingSetSize = 20): array;
    public function setCoord(CoordinateInterface $coord): PollutionDataFactoryInterface;
    public function setStation(Station $station): PollutionDataFactoryInterface;
}
