<?php

namespace AppBundle\Pollution\PollutionDataFactory;

use AppBundle\Entity\Data;
use AppBundle\Entity\Station;
use AppBundle\Pollution\Box\Box;
use AppBundle\Pollution\BoxDecorator\BoxDecoratorInterface;
use AppBundle\Pollution\DataList\DataList;
use AppBundle\Pollution\StationFinder\StationFinderInterface;
use AppBundle\Repository\DataRepository;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

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
     * @var StationFinderInterface $boxDecorator
     */
    protected $boxDecorator;

    /**
     * @var DataList $dataList
     */
    protected $dataList;

    public function __construct(Doctrine $doctrine, StationFinderInterface $stationFinder, BoxDecoratorInterface $boxDecorator)
    {
        $this->doctrine = $doctrine;
        $this->stationFinder = $stationFinder;
        $this->dataList = new DataList();
        $this->boxDecorator = $boxDecorator;
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
        return $this
            ->boxDecorator
            ->setBoxList($boxList)
            ->decorate()
            ->getBoxList()
        ;
    }
}
