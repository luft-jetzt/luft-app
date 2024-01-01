<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\StationCache\StationCacheInterface;
use Doctrine\Persistence\ManagerRegistry;

class ChainedPersister extends AbstractPersister
{
    protected array $persisterList;

    public function __construct(DataCacheInterface $dataCache, ManagerRegistry $managerRegistry, StationCacheInterface $stationCache)
    {
        $this->persisterList = [
            new CachePersister($dataCache, $stationCache),
            //new PostgisPersister($managerRegistry, $stationCache),
        ];

        parent::__construct($stationCache);
    }

    public function persistValues(array $values): PersisterInterface
    {
        /** @var PersisterInterface $persister */
        foreach ($this->persisterList as $persister) {
            $persister->persistValues($values);
        }

        return $this;
    }

    public function getNewValueList(): array
    {
        return [];
    }

    public function reset(): PersisterInterface
    {
        return $this;
    }
}
