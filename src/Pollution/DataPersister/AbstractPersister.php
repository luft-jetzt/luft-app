<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;

abstract class AbstractPersister implements PersisterInterface
{
    public function __construct(protected StationCacheInterface $stationCache)
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
