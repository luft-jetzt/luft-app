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

        array_walk($list, function(array $data = [], int $key) use (&$missingList) {
            if (0 === count($data) || null === $data) {
                array_push($missingList, $key);
            }
        });

        return $missingList;
    }

    public function addDataToList(DataListInterface $dataList, Data $data): bool
    {
        if (!$dataList->hasPollutant($data)) {
            $dataList->addData($data);

            return true;
        }

        return false;
    }
}
