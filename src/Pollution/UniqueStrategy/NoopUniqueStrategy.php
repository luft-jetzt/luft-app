<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;

class NoopUniqueStrategy implements UniqueStrategyInterface
{
    #[\Override]
    public function init(array $values): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function isDataDuplicate(Data $data): bool
    {
        return false;
    }

    #[\Override]
    public function addData(Data $data): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function save(): UniqueStrategyInterface
    {
        return $this;
    }
}