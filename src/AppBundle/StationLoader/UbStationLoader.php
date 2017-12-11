<?php

namespace AppBundle\StationLoader;

use AppBundle\Entity\Station;
use AppBundle\Repository\StationRepository;
use Curl\Curl;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManager;

class UbStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'https://www.umweltbundesamt.de/js/uaq/data/stations/limits';

    public function load(): array
    {
        $this->existingStationList = $this->getExistingStations();
        $newStationData = $this->fetchStationList();

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        foreach ($newStationData as $stationData) {
            if (!$this->stationExists($stationData[0], $this->existingStationList)) {
                $station = $this->createStation($stationData);

                $em->merge($station);

                $this->newStationList[] = $station;
            }
        }

        $em->flush();

        return $newStationData;
    }

    protected function fetchStationList(): array
    {
        $curl = new Curl();
        $curl->get(self::SOURCE_URL);
        $stationList = $curl->response->stations_idx;

        return $stationList;
    }

    protected function mergeStation(Station $station, array $stationData): Station
    {
        $station
            ->setTitle($stationData[1])
            ->setStationCode($stationData[0])
            ->setStateCode($stationData[2])
            ->setLatitude($stationData[5])
            ->setLongitude($stationData[4]);

        return $station;
    }

    protected function createStation(array $stationData): Station
    {
        $station = new Station($stationData[5], $stationData[4]);

        $this->mergeStation($station, $stationData);

        return $station;
    }
}
