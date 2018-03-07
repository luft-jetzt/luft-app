<?php declare(strict_types=1);

namespace App\SourceFetcher\Persister;

use App\Entity\Data;
use App\Entity\Station;
use App\SourceFetcher\Value\Value;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\Common\Persistence\ObjectManager;

class Persister
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var ObjectManager $entityManager */
    protected $entityManager;

    /** @var array $stationList */
    protected $stationList = [];

    /** @var array $newValueList */
    protected $newValueList = [];

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

            $this->newValueList[] = $data;
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

    public function getNewValueList(): array
    {
        return $this->newValueList;
    }
}
