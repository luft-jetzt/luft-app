<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Geocoding\Guesser\CityGuesserInterface;
use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DisplayController extends AbstractController
{
    public function indexAction(Request $request, SeoPage $seoPage, RequestConverterInterface $requestConverter, PollutionDataFactoryInterface $pollutionDataFactory, CityGuesserInterface $cityGuesser, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        /** @var Station $station */
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory->setCoord($station)->createDecoratedBoxList();

        if ($station->getCity()) {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s in %s', $station->getStationCode(), $station->getCity()->getName()));
        } else {
            $seoPage->setTitle(sprintf('Luftmesswerte für die Station %s', $station->getStationCode()));
        }

        $seoPage->setPreviewMap($station);

        return $this->render('Default/station.html.twig', [
            'station' => $station,
            'boxList' => $boxList,
        ]);
    }

    public function indexAction(Request $request, SeoPage $seoPage, PollutionDataFactory $pollutionDataFactory, StationFinderInterface $stationFinder): Response
    {
        $coord = $this->getCoordByRequest($request);
        $coord = $requestConverter->getCoordByRequest($request);

        if (!$coord) {
            return $this->redirectToRoute('frontpage');
        }

        $viewModelList = $pollutionDataFactory->setCoord($coord)->createDecoratedPollutantList();

        if (0 === count($viewModelList)) {
            return $this->noStationAction();
        }

        $cityName = $cityGuesser->guess($coord);

        if ($cityName) {
            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName));
            $city = $this->findCityForName($cityName);

            if ($city) {
                $breadcrumbs
                    ->addItem('Luft', $router->generate('display'))
                    ->addItem($city->getName(), $router->generate('show_city', ['citySlug' => $city->getSlug()]))
                    ->addItem('Dein Standort');
            }
        } else {
            $breadcrumbs
                ->addItem('Luft')
                ->addItem('Dein Standort');

            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus deiner Umgebung'));
            $city = null;
        }

        return $this->render('Default/display.html.twig', [
            'pollutantList' => $viewModelList,
            'cityName' => $cityName,
            'coord' => $coord,
            'city' => $city,
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
