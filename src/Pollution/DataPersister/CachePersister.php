<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Pollution\DataCache\DataCacheInterface;
use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\Value\Value;
use App\Pollution\ValueDataConverter\ValueDataConverter;

class CachePersister extends AbstractPersister
{
    protected DataCacheInterface $dataCache;

    public function __construct(DataCacheInterface $dataCache, StationCacheInterface $stationCache)
    {
        $this->dataCache = $dataCache;

        parent::__construct($stationCache);
    }

    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        /** @var Value $value */
        foreach ($values as $value) {
            if ($this->stationExists($value->getStation())) {
                $station = $this->getStationByCode($value->getStation());

                $data = ValueDataConverter::convert($value, $station);

                if (!$data) {
                    continue;
                }
            } else {
                continue;
            }

            $this->dataCache->addData($data);
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
