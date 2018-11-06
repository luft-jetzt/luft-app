<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Entity\TwitterSchedule;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends Controller
{
    protected function getStationListForCity(City $city): array
    {
        return $this->getDoctrine()->getRepository(Station::class)->findActiveStationsForCity($city);
    }

    protected function createBoxListForStationList(PollutionDataFactory $pollutionDataFactory, array $stationList): array
    {
        $stationsBoxList = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $stationsBoxList[$station->getStationCode()] = $pollutionDataFactory
                ->setStation($station)
                ->createDecoratedPollutantList();
        }

        return $stationsBoxList;
    }

    protected function getCityBySlug(string $citySlug): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);
    }

    protected function getTwitterScheduleByRequest(Request $request): ?TwitterSchedule
    {
        $scheduleId = $request->query->getInt('scheduleId');
        $schedule = $this->getDoctrine()->getRepository(TwitterSchedule::class)->find($scheduleId);

        return $schedule;
    }

    protected function findCityForName(string $cityName): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneByName($cityName);
    }
}
