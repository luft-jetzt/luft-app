<?php declare(strict_types=1);

namespace App\StationLoader;

use App\Entity\Station;
use Doctrine\ORM\EntityManager;

class StationLoader extends AbstractStationLoader
{
    public function process(callable $callback): StationLoaderInterface
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        foreach ($this->csv as $stationData) {
            $callback();

            if (!$stationData['station_code']) {
                continue;
            } elseif (!$this->stationExists($stationData['station_code'], $this->existingStationList)) {
                $station = $this->createStation($stationData);

                $em->merge($station);

                $this->newStationList[] = $station;
            } elseif ($this->update === true) {
                $station = $this->existingStationList[$stationData['station_code']];

                $station = $this->mergeStation($station, $stationData);
            }
        }

        $em->flush();

        return $this;
    }

    protected function mergeStation(Station $station, array $stationData): Station
    {
        $station
            ->setTitle($stationData['station_name'])
            ->setStationCode($stationData['station_code'])
            ->setLatitude(floatval($stationData['station_latitude_d']))
            ->setLongitude(floatval($stationData['station_longitude_d']))
            ->setFromDate($this->parseDate($stationData['station_start_date']))
            ->setUntilDate(!empty($stationData['station_end_date']) ? $this->parseDate($stationData['station_end_date']) :null)
            ->setAltitude(intval($stationData['station_altitude']))
            ->setStationType(!empty($stationData['type_of_station']) ? $stationData['type_of_station'] : null)
            ->setAreaType(!empty($stationData['station_type_of_area']) ? $stationData['station_type_of_area'] : null);

        return $station;
    }
}
