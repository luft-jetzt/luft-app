<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;
use App\ImportCache\ImportCacheInterface;

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
        return $this->importCache->has(Hasher::hashData($data));
    }

    public function addData(Data $data): UniqueStrategyInterface
    {
        $this->importCache->set(Hasher::hashData($data), (int) $data->getDateTime()->format('U'));

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
}
