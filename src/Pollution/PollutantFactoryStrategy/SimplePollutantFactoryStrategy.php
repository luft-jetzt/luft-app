<?php declare(strict_types=1);

namespace App\Pollution\PollutantFactoryStrategy;

use App\Entity\Data;
use App\Pollution\DataList\DataListInterface;

class SimplePollutantFactoryStrategy implements PollutantFactoryStrategyInterface
{
    #[\Override]
    public function getMissingPollutants(DataListInterface $dataList): array
    {
        $list = $dataList->getList();
        $missingList = [];

        array_walk($list, function(array $list, int $key) use (&$missingList) {
            if (null === $list || 0 === count($list)) {
                array_push($missingList, $key);
            }
        });

        return $missingList;
    }

    #[\Override]
    public function accepts(DataListInterface $dataList, Data $data = null): bool
    {
        if (!$data) {
            return false;
        }

        return $data && !$this->isSatisfied($dataList, $data->getPollutant());
    }

    #[\Override]
    public function addDataToList(DataListInterface $dataList, Data $data = null): bool
    {
        if (!$data) {
            return false;
        }

        $dataList->addData($data);

        return true;
    }

    #[\Override]
    public function isSatisfied(DataListInterface $dataList, int $pollutantId): bool
    {
        return $dataList->countPollutant($pollutantId) >= 1;
    }
}
