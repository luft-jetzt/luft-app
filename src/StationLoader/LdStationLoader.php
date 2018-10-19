<?php declare(strict_types=1);

namespace App\StationLoader;

use AppBundle\Entity\Station;
use Curl\Curl;
use Doctrine\ORM\EntityManager;

class LdStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'https://api.luftdaten.info/static/v2/data.dust.min.json';

    public function load(): array
    {
        $this->existingStationList = $this->getExistingStations();
        $newStationData = $this->fetchStationList();

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        foreach ($newStationData as $stationKey => $stationData) {
            if (!$this->stationExists($stationKey, $this->existingStationList)) {
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

        /** @var \stdClass $response */
        $response = $curl->response;

        $stationList = [];

        foreach ($response as $data) {
            $stationName = $this->composeStationName($data);

            $stationList[$stationName] = $data;
        }

        return $stationList;
    }

    protected function mergeStation(Station $station, \stdClass $stationData): Station
    {
        $station
            ->setStationCode($this->composeStationName($stationData))
            ->setLatitude($stationData->location->latitude)
            ->setLongitude($stationData->location->longitude)
        ;

        return $station;
    }

    protected function createStation(\stdClass $stationData): Station
    {
        $station = new Station(
            $stationData->location->latitude,
            $stationData->location->longitude
        );

        $this->mergeStation($station, $stationData);

        return $station;
    }

    protected function composeStationName(\stdClass $stationData): string
    {
        return sprintf('LDI%d', $stationData->location->id);
    }
}
