<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Geocoding\Guesser\CityGuesserInterface;
use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends AbstractController
{
    public function indexAction(Request $request, RequestConverterInterface $requestConverter, SeoPage $seoPage, PollutionDataFactoryInterface $pollutionDataFactory, CityGuesserInterface $cityGuesser): Response
    {
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
        } else {
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
