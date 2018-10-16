<?php declare(strict_types=1);

namespace App\StationLoader;

use App\Entity\Station;
use App\Repository\StationRepository;
use Curl\Curl;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use Doctrine\ORM\EntityManager;
use League\Csv\Reader;

class StationLoader
{
    const SOURCE_URL = 'https://www.env-it.de/stationen/public/download.do?event=euMetaStation';

    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var array $existingStationList */
    protected $existingStationList = [];

    /** @var array $newStationList */
    protected $newStationList = [];

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function load(): void
    {
        $this->existingStationList = $this->getExistingStations();

        $csv = $this->fetchStationList();

        $csv
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        foreach ($csv as $stationData) {
            if (!$this->stationExists($stationData['station_code'], $this->existingStationList)) {
                $station = $this->createStation($stationData);

                $em->merge($station);

                $this->newStationList[] = $station;
            }
        }

        $em->flush();
    }

    protected function getExistingStations(): array
    {
        /** @var StationRepository $stationRepository */
        $stationRepository = $this->doctrine->getRepository(Station::class);
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
            ->setStateCode(substr($stationData['station_code'], 2, 2))
            ->setLatitude(floatval($stationData['station_latitude_d']))
            ->setLongitude(floatval($stationData['station_longitude_d']));

        return $station;
    }

    protected function createStation(array $stationData): Station
    {
        $station = new Station(floatval($stationData['station_latitude_d']), floatval($stationData['station_longitude_d']));

        $this->mergeStation($station, $stationData);

        return $station;
    }

    protected function stationExists(string $stationCode, array $stationData): bool
    {
        echo  array_key_exists($stationCode, $stationData);
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
