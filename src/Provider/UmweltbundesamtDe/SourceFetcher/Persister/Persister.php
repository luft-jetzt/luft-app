<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Persister;

use App\Entity\Data;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Value\Value;

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
                ->setPollutant($value->getPollutant())
            ;

            if ($this->stationExists($value->getStation())) {
                $data->setStation($this->getStationByCode($value->getStation()));
            }

            $this->entityManager->persist($data);

            $this->newValueList[] = $data;
        }

        $this->entityManager->flush();

        return $this;
    }
}
