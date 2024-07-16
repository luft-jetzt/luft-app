<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Geocoding\Guesser\CityGuesserInterface;
use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\PollutionDataFactory\PollutionDataFactoryInterface;
use App\Air\SeoPage\SeoPage;
use App\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DisplayController extends AbstractController
{
    #[Route('/display', name: 'display', options: ['expose' => true], priority: 405)]
    public function indexAction(Request $request, SeoPage $seoPage, RequestConverterInterface $requestConverter, PollutionDataFactoryInterface $pollutionDataFactory, CityGuesserInterface $cityGuesser, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
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

    #[\Override]
    protected function findCityForName(string $cityName): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneByName($cityName);
    }
}
