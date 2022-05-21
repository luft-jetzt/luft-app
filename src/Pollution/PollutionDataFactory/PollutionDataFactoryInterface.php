<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Station;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use Caldera\GeoBasic\Coordinate\CoordinateInterface;

interface PollutionDataFactoryInterface
{
    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null, int $workingSetSize = 20): array;
    public function setCoord(CoordinateInterface $coord): PollutionDataFactoryInterface;
    public function setStation(Station $station): PollutionDataFactoryInterface;
    public function setStrategy(PollutantFactoryStrategyInterface $strategy): PollutionDataFactoryInterface;
}
