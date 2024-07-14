<?php declare(strict_types=1);

namespace App\Air\UniqueStrategy;

use App\Air\ImportCache\ImportCacheInterface;
use App\Entity\Data;

class CacheUniqueStrategy implements UniqueStrategyInterface
{
    public function __construct(protected ImportCacheInterface $importCache)
    {
    }

    #[\Override]
    public function init(array $values): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function isDataDuplicate(Data $data): bool
    {
        return $this->importCache->has(Hasher::hashData($data));
    }

    #[\Override]
    public function addData(Data $data): UniqueStrategyInterface
    {
        $this->importCache->set(Hasher::hashData($data), (int) $data->getDateTime()->format('U'));

        return $this;
    }

    #[\Override]
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

    #[\Override]
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
