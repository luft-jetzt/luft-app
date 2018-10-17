<?php declare(strict_types=1);

namespace App\StationLoader;

use App\Entity\Station;
use App\Repository\StationRepository;
use Curl\Curl;
use Symfony\Bridge\Doctrine\RegistryInterface;
use League\Csv\Reader;

abstract class AbstractStationLoader implements StationLoaderInterface
{
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

    public function load(): StationLoaderInterface
    {
        $this->existingStationList = $this->getExistingStations();

        $this->csv = $this->fetchStationList();

        $this->csv
            ->setDelimiter(';')
            ->setHeaderOffset(1);

        return $this;
    }

    public function count(): int
    {
        return $this->csv ? $this->csv->count() : 0;
    }

    public function setUpdate(bool $update = false): StationLoaderInterface
    {
        $this->update = $update;

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
        $curl->get(StationLoaderInterface::SOURCE_URL);

        $csv = Reader::createFromString($curl->response);

        return $csv;
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
