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

        array_walk($list, function(array $data, int $key) use (&$missingList) {
            if (null === $data || 2 >= count($data)) { // || 1 >= count($data)) {
                array_push($missingList, $key);
            }
        });

        var_dump($missingList);

        return $missingList;
    }

    public function accepts(DataListInterface $dataList, Data $data = null): bool
    {
        //return $data && 1 >= $dataList->countPollutant($data->getPollutant());

        return $data !== null;
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
