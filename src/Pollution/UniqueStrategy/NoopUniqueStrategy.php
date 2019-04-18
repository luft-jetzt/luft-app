<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;

class NoopUniqueStrategy implements UniqueStrategyInterface
{
    public function init(array $values): UniqueStrategyInterface
    {
        return $this;
    }

    public function isDataDuplicate(Data $data): bool
    {
        return false;
    }

    public function addData(Data $data): UniqueStrategyInterface
    {
        return $this;
    }

    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        return $this;
    }

    public function save(): UniqueStrategyInterface
    {
        return $this;
    }
}