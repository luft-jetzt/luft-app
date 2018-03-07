<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Entity\TwitterSchedule;
use App\Entity\Zip;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Pollution\StationFinder\StationFinderInterface;
use App\SeoPage\SeoPage;
use Caldera\GeoBasic\Coord\Coord;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends Controller
{
    protected function getCoordByRequest(Request $request): ?Coord
    {
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $zipCode = $request->query->get('zip');

        if (!$latitude && !$longitude && $zipCode) {
            $zip = $this->getDoctrine()->getRepository(Zip::class)->findOneByZip($zipCode);

            return $zip;
        }

        if ($latitude && $longitude && !$zipCode) {
            $coord = new Coord(
                $latitude,
                $longitude
            );

            return $coord;
        }

        return null;
    }

    protected function getStationFinder(): StationFinderInterface
    {
        return $this->get(ElasticStationFinder::class);
    }

    protected function getPollutionDataFactory(): PollutionDataFactory
    {
        return $this->get(PollutionDataFactory::class);
    }

    protected function getSeoPage(): SeoPage
    {
        return $this->get(SeoPage::class);
    }

    protected function getStationListForCity(City $city): array
    {
        return $this->getDoctrine()->getRepository(Station::class)->findByCity($city);
    }

    protected function createBoxListForStationList(array $stationList): array
    {
        $stationsBoxList = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $stationsBoxList[$station->getStationCode()] = $this->getPollutionDataFactory()->setCoord($station)->createDecoratedBoxList();
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
}
