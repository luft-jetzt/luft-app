<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Geocoding\Guesser\CityGuesserInterface;
use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Entity\City;
use App\Entity\Station;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Nicht-öffentliche Design-Vorschau für die Neugestaltung „Atmosphäre" (Prototyp 01).
 * Rendert die neuen Screens im echten Asset-Build mit echten Daten — zur Verifikation vor
 * der Live-Umstellung. Bewusst noindex; wird nach Abschluss der Migration entfernt.
 */
final class AtmosphaerePreviewController extends AbstractController
{
    #[Route('/vorschau/atmosphaere', name: 'atmosphaere_preview', methods: ['GET'])]
    public function index(): Response
    {
        return $this->noindex($this->render('Atmosphaere/preview.html.twig'));
    }

    #[Route('/vorschau/start', name: 'atmosphaere_preview_start', methods: ['GET'])]
    public function start(): Response
    {
        return $this->noindex($this->render('Atmosphaere/start.html.twig'));
    }

    #[Route('/vorschau/station/{stationCode}', name: 'atmosphaere_preview_station', methods: ['GET'])]
    public function station(
        #[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station,
        PollutionDataFactory $pollutionDataFactory,
        SeoPageInterface $seoPage,
    ): Response {
        $viewModelList = $pollutionDataFactory->setStation($station)->createDecoratedPollutantList();
        $seoPage->setTitle(sprintf('Luftqualität an der Station %s — luft.jetzt', $station->getStationCode()));

        return $this->noindex($this->render('Atmosphaere/station.html.twig', [
            'station' => $station,
            'pollutantList' => $viewModelList,
        ]));
    }

    #[Route('/vorschau/city/{citySlug}', name: 'atmosphaere_preview_city', methods: ['GET'])]
    public function city(
        #[MapEntity(expr: 'repository.findOneBySlug(citySlug)')] City $city,
        PollutionDataFactory $pollutionDataFactory,
        SeoPageInterface $seoPage,
    ): Response {
        $stationList = $this->getStationListForCity($city);
        $seoPage->setTitle(sprintf('Luftqualität in %s — luft.jetzt', $city->getName()));

        return $this->noindex($this->render('Atmosphaere/city.html.twig', [
            'city' => $city,
            'stationList' => $stationList,
            'stationBoxList' => $this->createViewModelListForStationList($pollutionDataFactory, $stationList),
        ]));
    }

    #[Route('/vorschau/result', name: 'atmosphaere_preview_result', methods: ['GET'])]
    public function result(
        Request $request,
        RequestConverterInterface $requestConverter,
        PollutionDataFactory $pollutionDataFactory,
        CityGuesserInterface $cityGuesser,
        SeoPageInterface $seoPage,
    ): Response {
        $coord = $requestConverter->getCoordByRequest($request);
        if (!$coord) {
            return $this->noindex($this->render('Atmosphaere/start.html.twig'));
        }

        $viewModelList = $pollutionDataFactory->setCoord($coord)->createDecoratedPollutantList();
        $cityName = $cityGuesser->guess($coord);
        $city = $cityName ? $this->findCityForName($cityName) : null;
        $seoPage->setTitle('Aktuelle Luftmesswerte aus deiner Umgebung — luft.jetzt');

        return $this->noindex($this->render('Atmosphaere/result.html.twig', [
            'pollutantList' => $viewModelList,
            'cityName' => $cityName,
            'coord' => $coord,
            'city' => $city,
        ]));
    }

    private function noindex(Response $response): Response
    {
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
