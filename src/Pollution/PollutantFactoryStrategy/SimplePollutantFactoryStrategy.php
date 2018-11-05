<?php declare(strict_types=1);

namespace App\Pollution\PollutantFactoryStrategy;

use App\Entity\Data;
use App\Pollution\DataList\DataListInterface;

class SimplePollutantFactoryStrategy implements PollutantFactoryStrategyInterface
{
    public function getMissingPollutants(DataListInterface $dataList): array
    {
        $list = $dataList->getList();
        $missingList = [];

        array_walk($list, function(Data $data = null, int $key) use (&$missingList) {
            if ($data === null) {
                array_push($missingList, $key);
            }
        });

        return $missingList;
    }

    public function addDataToList(DataListInterface $dataList, Data $data): bool
    {
        // TODO: Implement addDataToList() method.
    }
}
