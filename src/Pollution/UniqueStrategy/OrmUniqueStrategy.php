<?php declare(strict_types=1);

namespace App\Pollution\UniqueStrategy;

use App\Entity\Data;
use App\Pollution\Value\Value;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrmUniqueStrategy implements UniqueStrategyInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $existentDataList */
    protected $existentDataList = [];

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function init(array $values): UniqueStrategyInterface
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

        $existentDataList = $this->registry->getRepository(Data::class)->findHashsInterval($fromDateTime, $untilDateTime, array_unique($stationList));

        /** @var Data $data */
        foreach ($existentDataList as $key => $value) {
            $this->existentDataList[$value['hash']] = true;

            unset($existentDataList[$key]);
        }

        return $this;
    }

    public function isDataDuplicate(Data $data): bool
    {
        $hash = $this->hashData($data);

        return array_key_exists($hash, $this->existentDataList);
    }

    public function addData(Data $data): UniqueStrategyInterface
    {
        return $this;
    }

    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        return $this;
    }

    protected function hashData(Data $data): string
    {
        return $data->getStationId().$data->getDateTime()->format('U').$data->getPollutant().$data->getValue();
    }

    public function save(): UniqueStrategyInterface
    {
        return $this;
    }
}