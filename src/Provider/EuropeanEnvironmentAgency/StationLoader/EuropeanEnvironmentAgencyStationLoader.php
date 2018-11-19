<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\StationLoader;

use App\Entity\Station;
use App\Provider\AbstractStationLoader;
use App\Provider\EuropeanEnvironmentAgency\EuropeanEnvironmentAgencyProvider;
use App\Provider\StationLoaderInterface;
use App\Provider\UmweltbundesamtDe\UmweltbundesamtDeProvider;
use Curl\Curl;
use Doctrine\ORM\EntityManager;
use League\Csv\Reader;

class EuropeanEnvironmentAgencyStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'http://battuta.s3.amazonaws.com/eea-stations-all.json';

    /** @var array $stationDataList */
    protected $stationDataList;

    /** @var bool $update */
    protected $update = false;

    public function process(callable $callback): StationLoaderInterface
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        foreach ($this->stationDataList as $stationData) {
            $callback();

            if (!$stationData->stationId) {
                continue;
            } elseif (!$this->stationExists($stationData->stationId, $this->existingStationList)) {
                $station = $this->createStation($stationData);

                $em->merge($station);

                $this->newStationList[] = $station;
            } elseif ($this->update === true) {
                $station = $this->existingStationList[$stationData->stationId];

                $station = $this->mergeStation($station, $stationData);
            }
        }

        //$em->flush();

        return $this;
    }

    protected function mergeStation(Station $station, \stdClass $stationData): Station
    {
        $station
            ->setTitle($stationData->location)
            ->setStationCode($stationData->stationId)
            ->setLatitude(floatval($stationData->latitude))
            ->setLongitude(floatval($stationData->longitude));

        return $station;
    }

    public function load(): StationLoaderInterface
    {
        $this->existingStationList = $this->getExistingStationList('eea');

        $this->stationDataList = $this->fetchStationList();

        return $this;
    }

    public function count(): int
    {
        return count($this->stationDataList);
    }

    public function setUpdate(bool $update = false): StationLoaderInterface
    {
        $this->update = $update;

        return $this;
    }

    protected function fetchStationList(): array
    {
        $curl = new Curl();
        $curl->get(self::SOURCE_URL);

        $stationDataList = $curl->response;

        return $stationDataList;
    }

    protected function createStation(\stdClass $stationData): Station
    {
        $station = new Station(floatval($stationData->latitude), floatval($stationData->longitude));

        $this->mergeStation($station, $stationData);

        return $station;
    }

    public function getExistingStationList(): array
    {
        return $this->registry->getRepository(Station::class)->findIndexedByProvider(EuropeanEnvironmentAgencyProvider::IDENTIFIER);
    }
}
