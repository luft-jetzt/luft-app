<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Geocoding\Guesser\CityGuesserInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactoryInterface;
use App\SeoPage\SeoPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DisplayController extends AbstractController
{
    public function indexAction(Request $request, SeoPage $seoPage, PollutionDataFactoryInterface $pollutionDataFactory, CityGuesserInterface $cityGuesser, Breadcrumbs $breadcrumbs): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->redirectToRoute('frontpage');
        }

        $viewModelList = $pollutionDataFactory->setCoord($coord)->createDecoratedPollutantList();

        if (0 === count($viewModelList)) {
            return $this->noStationAction();
        }

        $cityName = $cityGuesser->guess($coord);

        if ($cityName) {
            /*$breadcrumbs
                ->addItem('Luft', $router->generate('display'))
                ->addItem($station->getCity()->getName(), $router->generate('show_city', ['citySlug' => $station->getCity()->getSlug()]))
                ->addItem(sprintf('Station %s', $station->getStationCode()));*/

            $seoPage->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName));
            $city = $this->findCityForName($cityName);
        } else {
/*            $breadcrumbs
                ->addItem('Luft')
                ->addItem(sprintf('Station %s', $station->getStationCode()));*/

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
