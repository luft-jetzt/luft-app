<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkAbstractController;

abstract class AbstractController extends FrameworkAbstractController
{
    public function __construct(protected ManagerRegistry $managerRegistry)
    {

    }

    /**
     * @deprecated 
     */
    public function getDoctrine(): ManagerRegistry
    {
        return $this->managerRegistry;
    }

    protected function getStationListForCity(City $city): array
    {
        return $this->getDoctrine()->getRepository(Station::class)->findActiveStationsForCity($city);
    }

    protected function createViewModelListForStationList(PollutionDataFactory $pollutionDataFactory, array $stationList): array
    {
        $stationViewModelList = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $stationViewModelList[$station->getStationCode()] = $pollutionDataFactory
                ->setStation($station)
                ->createDecoratedPollutantList();
        }

        return $stationViewModelList;
    }

    protected function getCityBySlug(string $citySlug): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);
    }

    protected function findCityForName(string $cityName): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneByName($cityName);
    }
}
