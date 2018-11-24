<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Geocoding\Query\GeoQueryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TypeaheadController extends AbstractController
{
    public function prefetchCitiesAction(RouterInterface $router): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        $data = [];

        /** @var City $city */
        foreach ($cityList as $city) {
            $url = $router->generate('show_city', ['citySlug' => $city->getSlug()]);

            $data[] = ['value' => [
                'url' => $url,
                'name' => $city->getName(),
                'icon' => 'university',
            ]];
        }

        return new JsonResponse($data);
    }

    public function prefetchStationsAction(RouterInterface $router): Response
    {
        $stationList = $this->getDoctrine()->getRepository(Station::class)->findActiveStations();

        $data = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $url = $router->generate('station', ['stationCode' => $station->getStationCode()]);

            $value = [
                'url' => $url,
                'name' => $station->getStationCode(),
                'icon' => 'thermometer-half',
            ];

            if ($station->getCity()) {
                $value['city'] = $station->getCity()->getName();
            }

            $data[] = ['value' => $value];
        }

        return new JsonResponse($data);
    }

    public function searchAction(Request $request, GeoQueryInterface $geoQuery): Response
    {
        $result = $geoQuery->query($request->query->get('query'));

        return new JsonResponse($result);
    }
}
