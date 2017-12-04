<?php

namespace AppBundle\Pollution\PollutionDataFactory;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\DataList\DataList;
use AppBundle\Pollution\Pollutant\CO;
use AppBundle\Pollution\Pollutant\NO2;
use AppBundle\Pollution\Pollutant\O3;
use AppBundle\Pollution\Pollutant\PM10;
use AppBundle\Pollution\Pollutant\PollutantInterface;
use AppBundle\Pollution\Pollutant\SO2;
use AppBundle\Pollution\StationFinder\ElasticStationFinder;
use AppBundle\Pollution\StationFinder\StationFinderInterface;
use AppBundle\Repository\DataRepository;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use FOS\ElasticaBundle\Finder\FinderInterface;

class PollutionDataFactory
{
    /**
     * @var Doctrine $doctrine
     */
    protected $doctrine;

    /**
     * @var CoordInterface $coord
     */
    protected $coord;

    /**
     * @var StationFinderInterface $stationFinder
     */
    protected $stationFinder;

    /**
     * @var DataList $dataList
     */
    protected $dataList;

    public function __construct(Doctrine $doctrine, StationFinderInterface $stationFinder)
    {
        $this->doctrine = $doctrine;
        $this->stationFinder = $stationFinder;
        $this->dataList = new DataList();
    }

    public function setCoord(CoordInterface $coord): PollutionDataFactory
    {
        $this->coord = $coord;

        return $this;
    }

    public function createDecoratedBoxList(): array
    {
        $stationList = $this->stationFinder->setCoord($this->coord)->findNearestStations();

        $dataList = $this->getDataListFromStationList($stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $boxList;
    }

    protected function getDataListFromStationList(array $stationList): array
    {
        foreach ($stationList as $station) {
            foreach ($this->dataList->getMissingPollutants() as $pollutant) {
                $data = $this->checkStationData($station, $pollutant);

                if ($data) {
                    $this->dataList->addData($data);
                }
            }
        }

        return $this->dataList->getList();
    }

    protected function checkStationData(Station $station, string $pollutant): ?Data
    {
        /** @var DataRepository $repository */
        $repository = $this->doctrine->getRepository(Data::class);

        return $repository->findLatestDataForStationAndPollutant($station, $pollutant);
    }

    protected function getPollutantById(int $pollutantId): PollutantInterface
    {
        switch ($pollutantId) {
            case 1: return new PM10();
            case 2: return new O3();
            case 3: return new NO2();
            case 4: return new SO2();
            case 5: return new CO();
        }
    }

    protected function getBoxListFromDataList(array $dataList): array
    {
        $boxList = [];

        foreach ($dataList as $data) {
            if ($data) {
                $boxList[] = new Box($data);
            }
        }

        return $boxList;
    }

    protected function decorateBoxList(array $boxList): array
    {
        /** @var Box $box */
        foreach ($boxList as $box) {
            $data = $box->getData();

            $pollutant = $this->getPollutantById($data->getPollutant());
            $level = $pollutant->getPollutionLevel()->getLevel($data);

            $box
                ->setStation($data->getStation())
                ->setPollutant($pollutant)
                ->setPollutionLevel($level)
            ;
        }

        return $boxList;
    }
}
