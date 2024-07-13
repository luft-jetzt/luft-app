<?php declare(strict_types=1);

namespace App\Air\UniqueStrategy;

use App\Air\Value\Value;
use App\Entity\Data;
use Doctrine\Persistence\ManagerRegistry;

class OrmUniqueStrategy implements UniqueStrategyInterface
{
    protected array $existentDataList = [];

    public function __construct(protected ManagerRegistry $registry)
    {
    }

    #[\Override]
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

    #[\Override]
    public function isDataDuplicate(Data $data): bool
    {
        return array_key_exists(Hasher::hashData($data), $this->existentDataList);
    }

    #[\Override]
    public function addData(Data $data): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function addDataList(array $dataList): UniqueStrategyInterface
    {
        return $this;
    }

    #[\Override]
    public function save(): UniqueStrategyInterface
    {
        return $this;
    }
}