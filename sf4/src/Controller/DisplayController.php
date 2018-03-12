<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Station;
use App\SeoPage\SeoPage;
use Caldera\GeoBasic\Coord\Coord;
use maxh\Nominatim\Nominatim;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends AbstractController
{
    public function stationAction(Request $request, string $stationCode): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $this->getPollutionDataFactory()->setCoord($station)->createDecoratedBoxList();

        if ($station->getCity()) {
            $this->getSeoPage()->setTitle(sprintf('Luftmesswerte für die Station %s in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $this->getSeoPage()->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'boxList' => $boxList,
        ]);
    }

    public function indexAction(Request $request): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('Default/select.html.twig');
        }

        $boxList = $this->getPollutionDataFactory()->setCoord($coord)->createDecoratedBoxList();

        if (0 === count($boxList)) {
            return $this->noStationAction($request, $coord);
        }

        $cityName = $this->getCityNameForCoord($coord);

        if ($cityName) {
            $this->getSeoPage()->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName));
        } else {
            $this->getSeoPage()->setTitle(sprintf('Aktuelle Luftmesswerte aus deiner Umgebung'));
        }

        return $this->render('Default/display.html.twig', [
            'boxList' => $boxList,
        ]);
    }

    public function noStationAction(Request $request, Coord $coord = null): Response
    {
        if (!$coord) {
            $coord = $this->getCoordByRequest($request);
        }

        $stationList = $this->getStationFinder()->setCoord($coord)->findNearestStations(1000.0);

        return $this->render('Default/nostations.html.twig', [
            'stationList' => $stationList,
        ]);
    }

    protected function getCityNameForCoord(Coord $coord): ?string
    {
        $url = "http://nominatim.openstreetmap.org/";
        $nominatim = new Nominatim($url);

        $reverse = $nominatim->newReverse()
            ->latlon($coord->getLatitude(), $coord->getLongitude())
        ;

        try {
            $result = $nominatim->find($reverse);

            return $result['address']['city'];
        } catch (\Exception $e) {
            return null;
        }
    }
}
