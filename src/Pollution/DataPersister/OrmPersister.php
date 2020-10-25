<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\Value\Value;
use App\Pollution\ValueDataConverter\ValueDataConverter;

class OrmPersister extends AbstractPersister
{
    public function persistValues(array $values): PersisterInterface
    {
        if (0 === count($values)) {
            return $this;
        }

        $this->uniqueStrategy->init($values);

        /** @var Value $value */
        foreach ($values as $value) {
            if ($this->stationExists($value->getStation())) {
                $station = $this->getStationByCode($value->getStation());

                $data = ValueDataConverter::convert($value, $station);
            } else {
                continue;
            }

            if ($this->uniqueStrategy->isDataDuplicate($data)) {
                continue;
            }

            $this->uniqueStrategy->addData($data);

            $this->entityManager->persist($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        $this->uniqueStrategy->save();

        return $this;
    }
}
