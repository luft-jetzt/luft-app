<?php declare(strict_types=1);

namespace App\Controller;

use App\Air\Geocoding\Geocoder\GeocoderInterface;
use App\Entity\City;
use App\Entity\Station;
use Geocoder\Exception\QuotaExceeded;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TypeaheadController extends AbstractController
{
    public function prefetchCitiesAction(RouterInterface $router): Response
    {
        $cityList = $this->managerRegistry->getRepository(City::class)->findAll();

        $data = [];

        /** @var City $city */
        foreach ($cityList as $city) {
            $url = $router->generate('show_city', ['citySlug' => $city->getSlug()]);

            $data[] = ['value' => [
                'url' => $url,
                'name' => $city->getName(),
            ]];
        }

        return new JsonResponse($data);
    }

    public function prefetchStationsAction(RouterInterface $router): Response
    {
        $stationList = $this->managerRegistry->getRepository(Station::class)->findActiveStations();

        $data = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $url = $router->generate('station', ['stationCode' => $station->getStationCode()]);

            $value = [
                'url' => $url,
                'stationCode' => $station->getStationCode(),
                'title' => $station->getTitle(),
            ];

            if ($station->getCity()) {
                $value['city'] = $station->getCity()->getName();
            }

            $data[] = ['value' => $value];
        }

        return new JsonResponse($data);
    }

    public function searchAction(Request $request, GeocoderInterface $geocoder): Response
    {
        try {
            $result = $geocoder->query($request->query->get('query'));
        } catch (QuotaExceeded) {
            return new JsonResponse(['error' => 'Die Suche ist momentan überlastet. Bitte versuche es in einigen Sekunden erneut.'], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return new JsonResponse($result);
    }
}
