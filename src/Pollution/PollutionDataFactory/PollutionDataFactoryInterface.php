<?php declare(strict_types=1);

namespace App\Pollution\PollutionDataFactory;

use App\Entity\Station;
use App\Pollution\PollutantFactoryStrategy\PollutantFactoryStrategyInterface;
use Caldera\GeoBasic\Coord\CoordInterface;

interface PollutionDataFactoryInterface
{
    public function createDecoratedPollutantList(\DateTime $dateTime = null, \DateInterval $dateInterval = null, int $workingSetSize = 1): array;
    public function setCoord(CoordInterface $coord): PollutionDataFactoryInterface;
    public function setStation(Station $station): PollutionDataFactoryInterface;
    public function setStrategy(PollutantFactoryStrategyInterface $strategy): PollutionDataFactoryInterface;
}
