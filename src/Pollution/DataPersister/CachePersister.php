<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use App\Pollution\Value\Value;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CachePersister extends AbstractPersister
{
    /** @var DataCacheInterface $dataCache */
    protected $dataCache;

    public function __construct(DataCacheInterface $dataCache, RegistryInterface $doctrine, StationCacheInterface $stationCache, UniqueStrategyInterface $uniqueStrategy)
    {
        $this->dataCache = $dataCache;

        parent::__construct($doctrine, $stationCache, $uniqueStrategy);
    }

    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        /** @var Value $value */
        foreach ($values as $value) {
            $data = new Data();

            $data
                ->setDateTime($value->getDateTime())
                ->setValue($value->getValue())
                ->setPollutant($value->getPollutant());

            if ($this->stationExists($value->getStation())) {
                $data->setStation($this->getStationByCode($value->getStation()));
            } else {
                continue;
            }

            $this->dataCache->addData($data);
        }
        
        return $this;
    }
}