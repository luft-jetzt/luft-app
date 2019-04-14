<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;

class OrmUniqueStrategy implements UniqueStrategyInterface
{

    public function init(): UniqueStrategyInterface
    {
        // TODO: Implement init() method.
    }

    public function isDataDuplicate(Data $data): bool
    {
        // TODO: Implement isDataDuplicate() method.
    }

    public function addData(Data $data): UniqueStrategyInterface
    {
        // TODO: Implement addData() method.
    }

    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        // TODO: Implement addDataList() method.
    }
}