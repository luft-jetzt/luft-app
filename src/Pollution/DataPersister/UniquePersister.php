<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\Value\Value;

class UniquePersister extends Persister implements UniquePersisterInterface
{
    /** @var array $existentDataList */
    protected $existentDataList = [];

    /** @var array $duplicateDataList */
    protected $duplicateDataList = [];

    protected function fetchExistentData(array $values): UniquePersister
    {
        $fromDateTime = null;
        $untilDateTime = null;

        /** @var Value $value */
        foreach ($values as $value) {
            if ($fromDateTime === null || $fromDateTime > $value->getDateTime()) {
                $fromDateTime = $value->getDateTime();
            }

            if ($untilDateTime === null || $untilDateTime < $value->getDateTime()) {
                $untilDateTime = $value->getDateTime();
            }
        }

        $existentDataList = $this->doctrine->getRepository(Data::class)->findInInterval($fromDateTime, $untilDateTime);

        /** @var Data $data */
        foreach ($existentDataList as $data) {
            $this->existentDataList[$this->hashData($data)] = $data;
        }

        return $this;
    }

    protected function hashData(Data $data): string
    {
        return md5($data->getStationId().$data->getDateTime()->format('U').$data->getPollutant().$data->getValue());
    }

    protected function dataExists(Data $data): bool
    {
        return array_key_exists($this->hashData($data), $this->existentDataList);
    }

    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        $this->fetchStationList();
        $this->fetchExistentData($values);

        /** @var Value $value */
        foreach ($values as $value) {
            $data = new Data();

            $data
                ->setDateTime($value->getDateTime())
                ->setValue($value->getValue())
                ->setPollutant($value->getPollutant());

            if ($this->stationExists($value->getStation())) {
                $data->setStation($this->getStationByCode($value->getStation()));
            } else {
                continue;
            }

            if ($this->dataExists($data)) {
                $this->duplicateDataList[] = $data;

                continue;
            }

            $this->entityManager->persist($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        return $this;
    }

    public function getDuplicateDataList(): array
    {
        return $this->duplicateDataList;
    }
}
