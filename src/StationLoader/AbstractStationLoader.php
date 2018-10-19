<?php declare(strict_types=1);

namespace App\StationLoader;

use AppBundle\Entity\Station;
use AppBundle\Repository\StationRepository;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

abstract class AbstractStationLoader implements StationLoaderInterface
{
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

    protected function getExistingStations(): array
    {
        /** @var StationRepository $stationRepository */
        $stationRepository = $this->doctrine->getRepository(Station::class);

        return $stationRepository->findAllIndexed();
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
