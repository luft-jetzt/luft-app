<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;
use App\ImportCache\ImportCacheInterface;
use App\Pollution\Value\Value;

class CacheUniqueStrategy implements UniqueStrategyInterface
{
    /** @var ImportCacheInterface */
    protected $importCache;

    public function __construct(ImportCacheInterface $importCache)
    {
        $this->importCache = $importCache;
    }

    public function init(array $values): UniqueStrategyInterface
    {
        return $this;
    }

    public function isDataDuplicate(Data $data): bool
    {
        $hash = $this->hashData($data);

        return $this->importCache->has($hash);
    }

    public function addData(Data $data): UniqueStrategyInterface
    {
        $hash = $this->hashData($data);

        $this->importCache->add($hash, $data->getDateTime()->format('U'));

        return $this;
    }

    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        /** @var Data $data */
        foreach ($dataList as $key => $data) {
            $this->addData($data);
        }

        return $this;
    }

    public function getDataList(): array
    {
        return [];
    }

    public function save(): UniqueStrategyInterface
    {
        return $this;
    }

    public function clear(): CacheUniqueStrategy
    {
        $this->importCache->clear();

        return $this;
    }

    protected function hashData(Data $data): string
    {
        return $data->getStationId() . $data->getDateTime()->format('U') . $data->getPollutant() . $data->getValue();
    }

    protected function hashValue(Value $value): string
    {
        return $value->getStation() . $value->getDateTime()->format('U') . $value->getPollutant() . $value->getValue();
    }
}