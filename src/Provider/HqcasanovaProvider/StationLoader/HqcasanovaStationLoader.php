<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\StationLoader;

use App\Entity\Station;
use App\Provider\AbstractStationLoader;
use App\Provider\HqcasanovaProvider\HqcasanovaProvider;
use App\Provider\StationLoaderInterface;
use Doctrine\ORM\EntityManager;

class HqcasanovaStationLoader extends AbstractStationLoader
{
    public function process(callable $callback): StationLoaderInterface
    {
        $station = $this->createStation();

        if (!$this->stationExists($station->getStationCode())) {

            /** @var EntityManager $em */
            $em = $this->registry->getManager();

            $em->persist($station);

            $this->newStationList[$station->getStationCode()] = $station;

            $em->flush();
        }

        return $this;
    }

    protected function createStation(): Station
    {
        $station = new Station(19.536342, -155.576480);
        $station
            ->setAltitude(3397)
            ->setStationCode('USHIMALO')
            ->setTitle('Mauna Loa Observatory')
            ->setProvider(HqcasanovaProvider::IDENTIFIER);

        return $station;
    }

    public function getExistingStationList(): array
    {
        return $this->registry->getRepository(Station::class)->findIndexedByProvider(HqcasanovaProvider::IDENTIFIER);
    }

    public function load(): StationLoaderInterface
    {
        $this->existingStationList = $this->getExistingStationList();

        return $this;
    }

    public function count(): int
    {
        return 1;
    }

    public function setUpdate(bool $update = false): StationLoaderInterface
    {
        return $this;
    }
}
