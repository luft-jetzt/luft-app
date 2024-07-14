<?php declare(strict_types=1);

namespace App\Air\Analysis\CoronaFireworksAnalysis;

use App\Entity\Data;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Persistence\ManagerRegistry;

class ValueFetcher implements ValueFetcherInterface
{
    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }

    #[\Override]
    public function fetchValues(CoordInterface $coord, array $yearList = [], int $startHour = 12, int $rangeInMinutes = 1440, float $maxDistance = 15): array
    {
        return $this->managerRegistry->getRepository(Data::class)->findDataForCoronaFireworksAnalysis($coord);
    }
}
