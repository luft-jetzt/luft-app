<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Geocoding\Guesser\CityGuesserInterface;
use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\PollutionDataFactory\PollutionDataFactoryInterface;
use App\Air\SeoPage\SeoPage;
use App\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DisplayController extends AbstractController
{
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

        $seoPage->setStandardPreviewPhoto();

        if ($cityName) {
            $seoPage
                ->setTitle(sprintf('Aktuelle Luftmesswerte aus %s', $cityName))
                ->setDescription(sprintf('Aktuelle Luftmesswerte an deinem Standort in %s: Feinstaub, Stickstoffdioxid und Ozon mit Bewertung nach Umweltbundesamt-Grenzwerten.', $cityName));
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

            $seoPage
                ->setTitle(sprintf('Aktuelle Luftmesswerte aus deiner Umgebung'))
                ->setDescription('Aktuelle Luftmesswerte aus deiner Umgebung: Feinstaub, Stickstoffdioxid und Ozon mit Bewertung nach Umweltbundesamt-Grenzwerten.');
            $city = null;
        }

        return $this->render('Atmosphaere/result.html.twig', [
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
        return $this->managerRegistry->getRepository(City::class)->findOneByName($cityName);
    }
}
