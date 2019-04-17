<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;

interface UniqueStrategyInterface
{
    public function init(array $values): UniqueStrategyInterface;
    public function isDataDuplicate(Data $data): bool;
    public function addData(Data $data): UniqueStrategyInterface;
    public function addDataList(array $dataList): UniqueStrategyInterface;
}