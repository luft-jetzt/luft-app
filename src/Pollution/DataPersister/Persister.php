<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\Value\Value;

class Persister extends AbstractPersister
{
    public function persistValues(array $values): PersisterInterface
    {
        /** @var Value $value */
        foreach ($values as $value) {
            $data = new Data();

            $data
                ->setDateTime($value->getDateTime())
                ->setValue($value->getValue())
                ->setPollutant($value->getPollutant());

            if ($this->stationExists($value->getStation())) {
                $data->setStation($this->stationCache->getStationReferenceByCode($value->getStation()));
            } else {
                continue;
            }

            $this->entityManager->merge($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        return $this;
    }
}
