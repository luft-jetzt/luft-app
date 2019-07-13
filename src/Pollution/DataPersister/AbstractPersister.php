<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractPersister implements PersisterInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var ObjectManager $entityManager */
    protected $entityManager;

    /** @var array $stationList */
    protected $stationList = [];

    /** @var array $newValueList */
    protected $newValueList = [];

    /** @var StationCacheInterface $stationCache */
    protected $stationCache;

    /** @var UniqueStrategyInterface $uniqueStrategy */
    protected $uniqueStrategy;

    public function __construct(RegistryInterface $doctrine, StationCacheInterface $stationCache, UniqueStrategyInterface $uniqueStrategy)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();
        $this->stationCache = $stationCache;
        $this->uniqueStrategy = $uniqueStrategy;
    }

    protected function stationExists(string $stationCode): bool
    {
        return $this->stationCache->stationExists($stationCode);
    }

    protected function getStationByCode(string $stationCode): Station
    {
        return $this->stationCache->getStationReferenceByCode($stationCode);
    }

    public function reset(): PersisterInterface
    {
        $this->stationList = [];
        $this->newValueList = [];

        return $this;
    }

    public function getNewValueList(): array
    {
        return $this->newValueList;
    }
}
