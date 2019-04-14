<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;

class OrmUniqueStrategy implements UniqueStrategyInterface
{

    public function init(): UniqueStrategyInterface
    {
        $fromDateTime = null;
        $untilDateTime = null;
        $stationList = [];

        /** @var Value $value */
        foreach ($values as $value) {
            if ($fromDateTime === null || $fromDateTime > $value->getDateTime()) {
                $fromDateTime = $value->getDateTime();
            }

            if ($untilDateTime === null || $untilDateTime < $value->getDateTime()) {
                $untilDateTime = $value->getDateTime();
            }

            $stationList[] = $value->getStation();
        }

        $existentDataList = $this->doctrine->getRepository(Data::class)->findHashsInterval($fromDateTime, $untilDateTime, array_unique($stationList));

        /** @var Data $data */
        foreach ($existentDataList as $key => $value) {
            $this->existentDataList[$value['hash']] = true;

            unset($existentDataList[$key]);
        }

        return $this;
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

    protected function hashData(Data $data): string
    {
        return $data->getStationId().$data->getDateTime()->format('U').$data->getPollutant().$data->getValue();
    }
}