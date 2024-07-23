<?php declare(strict_types=1);

namespace App\Air\DataPersister;

use App\Air\StationCache\StationCacheInterface;
use App\Entity\Station;

abstract class AbstractPersister implements PersisterInterface
{
    public function __construct(protected readonly StationCacheInterface $stationCache)
    {

    }

    protected function stationExists(string $stationCode): bool
    {
        return $this->stationCache->stationExists($stationCode);
    }

    protected function getStationByCode(string $stationCode): Station
    {
        return $this->stationCache->getStationReferenceByCode($stationCode);
    }
}
