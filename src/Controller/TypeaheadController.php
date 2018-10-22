<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Zip;
use Curl\Curl;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TypeaheadController extends AbstractController
{
    public function prefetchAction(RouterInterface $router): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        $data = [];

        /** @var City $city */
        foreach ($cityList as $city) {
            $url = $router->generate('show_city', ['citySlug' => $city->getSlug()]);

            $data[] = ['value' => [
                'url' => $url,
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
                'name' => $city->getName(),
            ]];
        }

        return new JsonResponse($data);
    }

    public function searchAction(Request $request, RouterInterface $router): Response
    {
        $queryString = $request->query->get('query');

        $curl = new Curl();
        $curl->get(sprintf('https://photon.komoot.de/api/?q=%s&lang=de', $queryString));

        $features = $curl->response->features;

        $result = [];

        foreach ($features as $feature) {
            $latitude = $feature->geometry->coordinates[1];
            $longitude = $feature->geometry->coordinates[0];
            $url = $router->generate('display', ['latitude' => $latitude, 'longitude' => $longitude]);

            $result[] = [
                'value' => [
                    'url' => $url,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'name' => $feature->properties->name,
                    //'zipCode' => $feature->properties->postcode,
                ]
            ];
        }

        return new JsonResponse($result);
    }
}
