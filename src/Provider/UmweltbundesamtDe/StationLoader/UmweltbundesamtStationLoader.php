<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\StationLoader;

use App\DBAL\Types\AreaType;
use App\DBAL\Types\StationType;
use App\Entity\Station;
use App\Provider\AbstractStationLoader;
use App\Provider\StationLoaderInterface;
use App\Provider\UmweltbundesamtDe\UmweltbundesamtDeProvider;
use Curl\Curl;
use Doctrine\ORM\EntityManager;

class UmweltbundesamtStationLoader extends AbstractStationLoader
{
    const SOURCE_URL = 'https://www.umweltbundesamt.de/api/air_data/v2/meta/json?use=measure&lang=de';

    const FIELD_ID = 0;
    const FIELD_STATION_CODE = 1;
    const FIELD_TITLE = 2;
    const FIELD_CITY = 3;
    const FIELD_START_DATE = 5;
    const FIELD_LONGITUDE = 7;
    const FIELD_LATITUDE = 8;
    const FIELD_STATE_CODE = 12;
    const FIELD_STATE = 13;
    const FIELD_AREA_TYPE = 15;
    const FIELD_STATION_TYPE = 16;

    /** @var bool $update */
    protected $update = false;

    /** @var array $ubaStationList */
    protected $ubaStationList = [];

    public function process(callable $callback): StationLoaderInterface
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        foreach ($this->ubaStationList as $stationData) {
            $callback();

            if (!array_key_exists(self::FIELD_STATION_CODE, $stationData) || !$stationData[self::FIELD_STATION_CODE]) {
                continue;
            }

            $stationCode = $stationData[self::FIELD_STATION_CODE];

            if (!$this->stationExists($stationCode)) {
                    $station = $this->createStation($stationData);

                    $em->persist($station);

                    $this->newStationList[] = $station;
                } elseif ($this->update === true) {
                    $station = $this->existingStationList[$stationCode];

                    $station = $this->mergeStation($station, $stationData);
                }
            }

        $em->flush();

        return $this;
    }

    protected function mergeStation(Station $station, array $stationData): Station
    {
        $station
            ->setTitle($stationData[self::FIELD_TITLE])
            ->setProvider(UmweltbundesamtDeProvider::IDENTIFIER)
            ->setStationCode($stationData[self::FIELD_STATION_CODE])
            ->setLatitude((float) $stationData[self::FIELD_LATITUDE])
            ->setLongitude((float) $stationData[self::FIELD_LONGITUDE])
            ->setFromDate(new \DateTime($stationData[self::FIELD_START_DATE]))
            ->setStationType($this->mapStationType($stationData[self::FIELD_STATION_TYPE]))
            ->setAreaType($this->mapAreaType($stationData[self::FIELD_AREA_TYPE]))
            ->setUbaStationId($stationData[self::FIELD_ID]);

        return $station;
    }

    public function load(): StationLoaderInterface
    {
        $this->existingStationList = $this->getExistingStationList('uba_de');

        $this->fetchStationList();

        return $this;
    }

    public function count(): int
    {
        return count($this->ubaStationList);
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

        $this->ubaStationList = (json_decode($curl->rawResponse, true))['stations'];

        return $this->ubaStationList;
    }

    protected function createStation(array $stationData): Station
    {
        $latitude = (float) $stationData[self::FIELD_LATITUDE];
        $longitude = (float) $stationData[self::FIELD_LONGITUDE];

        $station = new Station($latitude, $longitude);

        $this->mergeStation($station, $stationData);

        return $station;
    }

    public function getExistingStationList(): array
    {
        return $this->registry->getRepository(Station::class)->findIndexedByProvider(UmweltbundesamtDeProvider::IDENTIFIER);
    }

    protected function mapAreaType(string $areaType): string
    {
        switch ($areaType) {
            case 'vorstädtisch': return AreaType::SUBURBAN;
            case 'städtisch': return AreaType::URBAN;
            case 'ländlich': return AreaType::RURAL;
        }
    }

    protected function mapStationType(string $stationType): string
    {
        switch ($stationType) {
            case 'Hintergrund': return StationType::BACKGROUND;
            case 'Verkehr': return StationType::TRAFFIC;
            case 'Industrie': return StationType::INDUSTRIAL;
            default: dump($stationType);
        }
    }
}
