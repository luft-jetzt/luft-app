<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Geocoding\CityGuesserInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DisplayController extends AbstractController
{
    public function stationAction(SeoPage $seoPage, string $stationCode, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory->setCoord($station)->createDecoratedBoxList();

        if ($station->getCity()) {
            $breadcrumbs
                ->addItem('Luft', $router->generate('display'))
                ->addItem('Hamburg', $router->generate('show_city', ['citySlug' => $station->getCity()->getSlug()]))
                ->addItem(sprintf('Station %s', $station->getStationCode()));

            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $breadcrumbs
                ->addItem('Luft')
                ->addItem(sprintf('Station %s', $station->getStationCode()));

            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'boxList' => $boxList,
        ]);
    }

    public function indexAction(Request $request, SeoPage $seoPage, PollutionDataFactory $pollutionDataFactory, CityGuesserInterface $cityGuesser, Breadcrumbs $breadcrumbs): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('Default/select.html.twig');
        }

        $boxList = $pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

        if (0 === count($boxList)) {
            return $this->noStationAction();
        }

        $cityName = $cityGuesser->guess($coord);

        if ($cityName) {
            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName));
        } else {
            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus deiner Umgebung'));
        }

        return $this->render('Default/display.html.twig', [
            'boxList' => $boxList,
            'cityName' => $cityName,
            'city' => $this->findCityForName($cityName),
        ]);
    }

    public function noStationAction(): Response
    {
        return $this->render('Default/no_stations.html.twig');
    }

    protected function findCityForName(string $cityName): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneByName($cityName);
    }
}
