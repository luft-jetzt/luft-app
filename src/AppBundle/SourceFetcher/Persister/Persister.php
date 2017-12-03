<?php

namespace AppBundle\SourceFetcher\Persister;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\SourceFetcher\Value\Value;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class Persister
{
    protected $doctrine;
    protected $entityManager;
    protected $stationList = [];

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getManager();

        $this->fetchStationList();
    }

    public function persistValues(array $values): Persister
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
        }

        $this->entityManager->flush();

        return $this;
    }

    protected function fetchStationList(): Persister
    {
        $stations = $this->doctrine->getRepository(Station::class)->findAll();

        /** @var Station $station */
        foreach ($stations as $station) {
            $this->stationList[$station->getStationCode()] = $station;
        }

        return $this;
    }

    protected function stationExists(string $stationCode): bool
    {
        return array_key_exists($stationCode, $this->stationList);
    }

    protected function getStationByCode(string $stationCode): Station
    {
        return $this->stationList[$stationCode];
    }
}
