<?php declare(strict_types=1);

namespace App\StationLoader;

use App\Entity\Station;
use App\Repository\StationRepository;
use Curl\Curl;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManager;
use League\Csv\Reader;

class StationLoader
{
    const SOURCE_URL = 'https://www.env-it.de/stationen/public/download.do?event=euMetaStation';

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $existingStationList */
    protected $existingStationList = [];

    /** @var array $newStationList */
    protected $newStationList = [];

    /** @var Reader $csv */
    protected $csv;

    /** @var bool $update */
    protected $update = false;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function load(): StationLoader
    {
        $this->existingStationList = $this->getExistingStations();

        $this->csv = $this->fetchStationList();

        $this->csv
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        return $this;
    }

    public function count(): int
    {
        return $this->csv ? $this->csv->count() : 0;
    }

    public function setUpdate(bool $update = false): StationLoader
    {
        $this->update = $update;

        return $this;
    }

    public function process(callable $callback): StationLoader
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();

        foreach ($this->csv as $stationData) {
            $callback();

            if (!$this->stationExists($stationData['station_code'], $this->existingStationList)) {
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

    protected function getExistingStations(): array
    {
        /** @var StationRepository $stationRepository */
        $stationRepository = $this->registry->getRepository(Station::class);

        return $stationRepository->findAllIndexed();
    }

    protected function fetchStationList(): Reader
    {
        $curl = new Curl();
        $curl->get(self::SOURCE_URL);

        //$csv = Reader::createFromString($curl->getResponse());
        $csv = Reader::createFromPath('/var/www/station.csv');

        return $csv;
    }

    protected function mergeStation(Station $station, array $stationData): Station
    {
        $station
            ->setTitle($stationData['station_name'])
            ->setStationCode($stationData['station_code'])
            ->setStateCode($this->parseStateCode($stationData['station_code']))
            ->setLatitude(floatval($stationData['station_latitude_d']))
            ->setLongitude(floatval($stationData['station_longitude_d']))
            ->setFromDate($this->parseDate($stationData['station_start_date']))
            ->setUntilDate($this->parseDate($stationData['station_end_date']))
            ->setAltitude(intval($stationData['station_altitude']));

        return $station;
    }

    protected function parseStateCode(string $stationCode): string
    {
        return substr($stationCode, 2, 2);
    }

    protected function parseDate(string $dateString): \DateTime
    {
        sscanf($dateString,'%4d%2d%2d', $year, $month, $day);

        return new \DateTime(sprintf('%d-%d-%d', $year, $month, $day));
    }

    protected function createStation(array $stationData): Station
    {
        $station = new Station(floatval($stationData['station_latitude_d']), floatval($stationData['station_longitude_d']));

        $this->mergeStation($station, $stationData);

        return $station;
    }

    protected function stationExists(string $stationCode, array $stationData): bool
    {
        return array_key_exists($stationCode, $stationData);
    }

    public function getExistingStationList(): array
    {
        return $this->existingStationList;
    }

    public function getNewStationList(): array
    {
        return $this->newStationList;
    }
}
