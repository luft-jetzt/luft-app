<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;

abstract class AbstractPersister implements PersisterInterface
{
    protected StationCacheInterface $stationCache;

    public function __construct(StationCacheInterface $stationCache)
    {
        $this->stationCache = $stationCache;
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
