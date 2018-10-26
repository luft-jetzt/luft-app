<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Geocoding\Query\GeoQueryInterface;
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
                'name' => $city->getName(),
                'icon' => 'university',
            ]];
        }

        return new JsonResponse($data);
    }

    public function searchAction(Request $request, GeoQueryInterface $geoQuery): Response
    {
        $result = $geoQuery->query($request->query->get('query'));

        return new JsonResponse($result);
    }
}
