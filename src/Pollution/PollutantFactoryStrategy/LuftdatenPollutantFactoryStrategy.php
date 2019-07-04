<?php declare(strict_types=1);

namespace App\Pollution\PollutantFactoryStrategy;

use App\Air\Measurement\MeasurementInterface;
use App\Entity\Data;
use App\Pollution\DataList\DataListInterface;

class LuftdatenPollutantFactoryStrategy implements PollutantFactoryStrategyInterface
{
    public function getMissingPollutants(DataListInterface $dataList): array
    {
        $list = $dataList->getList();
        $missingList = [];

        array_walk($list, function(array $list, int $key) use (&$missingList) {
            if (null === $list || 0 === count($list) || 1 === count($list)) {
                array_push($missingList, $key);
            }
        });

        return $missingList;
    }

    public function accepts(DataListInterface $dataList, Data $data = null): bool
    {
        if (!$data) {
            return false;
        }

        if ($dataList->countPollutant($data->getPollutant()) === 2) {
            return false;
        }

        if ($dataList->countPollutant($data->getPollutant()) === 0) {
            return true;
        }

        $existingData = $this->getExistingSingleData($dataList, $data->getPollutant());

        return $this->isProvidersDifferent($existingData, $data);
    }

    public function isSatisfied(DataListInterface $dataList, int $pollutantId): bool
    {
        // Luftdaten only collects pm10 and pm25, so there is no need to wait for o3, no2 or so2
        if (in_array($pollutantId, [MeasurementInterface::MEASUREMENT_PM10, MeasurementInterface::MEASUREMENT_PM25])) {
            return $dataList->countPollutant($pollutantId) >= 2;
        } else {
            return $dataList->countPollutant($pollutantId) >= 1;
        }
    }

    public function addDataToList(DataListInterface $dataList, Data $data = null): bool
    {
        if (!$data) {
            return false;
        }

        $dataList->addData($data);

        return true;
    }

    protected function getExistingSingleData(DataListInterface $dataList, int $pollutant): ?Data
    {
        $list = $dataList->getList()[$pollutant];

        return array_pop($list);
    }

    protected function isProvidersDifferent(Data $a, Data $b): bool
    {
        return $a->getStation()->getProvider() !== $b->getStation()->getProvider();
    }
}
