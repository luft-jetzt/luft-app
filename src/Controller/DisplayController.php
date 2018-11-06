<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Geocoding\Guesser\CityGuesserInterface;
use App\Geocoding\Query\GeoQueryInterface;
use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends AbstractController
{
    public function indexAction(Request $request, RequestConverterInterface $requestConverter, SeoPage $seoPage, GeoQueryInterface $geoQuery, PollutionDataFactory $pollutionDataFactory, CityGuesserInterface $cityGuesser): Response
    {
        $coord = $requestConverter->getCoordByRequest($request);

        if (!$coord) {
            $seoPage->setStandardPreviewPhoto();
            
            return $this->render('Default/select.html.twig');
        }

        $boxList = $pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

        if (0 === count($boxList)) {
            return $this->noStationAction();
        }

        $cityName = $cityGuesser->guess($coord);

        if ($cityName) {
            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName));
            $city = $this->findCityForName($cityName);
        } else {
            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus deiner Umgebung'));
            $city = null;
        }

        return $this->render('Default/display.html.twig', [
            'pollutantList' => $boxList,
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
