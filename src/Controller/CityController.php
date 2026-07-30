<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Geocoding\Geocoder\GeocoderInterface;
use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Air\SeoPage\SeoPage;
use App\Entity\City;
use App\Entity\Station;
use App\Repository\StationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CityController extends AbstractController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly GeocoderInterface $geocoder,
        private readonly ?LoggerInterface $logger = null,
    ) {
        parent::__construct($managerRegistry);
    }

    public function showAction(#[MapEntity(expr: 'repository.findOneBySlug(citySlug)')] City $city, SeoPage $seoPage, PollutionDataFactory $pollutionDataFactory, Breadcrumbs $breadcrumbs, RouterInterface $router): Response
    {
        $seoPage
            ->setTitle(sprintf('Luftmesswerte aus %s: Stickstoffdioxid, Feinstaub und Ozon', $city->getName()))
            ->setDescription(sprintf('Aktuelle Schadstoffwerte aus Luftmessstationen in %s: Stickstoffdioxid, Feinstaub und Ozon', $city->getName()))
            ->setStandardPreviewPhoto();

        $breadcrumbs
            ->addItem('Luft', $router->generate('display'))
            ->addItem($city->getName(), $router->generate('show_city', ['citySlug' => $city->getSlug()]));

        $stationList = $this->getStationListForCity($city);

        // Datenlücke: fast keine Station ist per FK mit einer Stadt verknüpft. Fällt die
        // FK-Abfrage leer aus, den Stadtnamen geocoden und die nächstgelegenen aktiven
        // Stationen im Umkreis anzeigen (statt „Keine Daten").
        if (0 === count($stationList)) {
            $stationList = $this->findNearbyStationsForCity($city);
        }

        $stationViewModelList = $this->createViewModelListForStationList($pollutionDataFactory, $stationList);

        return $this->render('Atmosphaere/city.html.twig', [
            'city' => $city,
            'stationList' => $stationList,
            'stationBoxList' => $stationViewModelList,
        ]);
    }

    /**
     * @return Station[]
     */
    private function findNearbyStationsForCity(City $city): array
    {
        try {
            $results = $this->geocoder->query(sprintf('%s, Deutschland', $city->getName()));
        } catch (\Throwable $exception) {
            $this->logger?->warning('Geocoding der Stadt für den Stationsumkreis fehlgeschlagen.', [
                'city' => $city->getName(),
                'exception' => $exception->getMessage(),
            ]);

            return [];
        }

        foreach ($results as $result) {
            $value = $result['value'] ?? [];

            if (isset($value['latitude'], $value['longitude'])) {
                $repository = $this->managerRegistry->getRepository(Station::class);
                \assert($repository instanceof StationRepository);

                return $repository->findActiveStationsNearCoord((float) $value['latitude'], (float) $value['longitude']);
            }
        }

        return [];
    }
}
