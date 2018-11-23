<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Station;
use App\Pollution\StationCache\StationCacheInterface;
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

    public function __construct(RegistryInterface $doctrine, StationCacheInterface $stationCache)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();
        $this->stationCache = $stationCache;
    }

    protected function stationExists(string $stationCode): bool
    {
        return $this->stationCache->stationExists($stationCode);
    }

    protected function getStationByCode(string $stationCode): Station
    {
        return $this->stationCache->getStationByCode($stationCode);
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
