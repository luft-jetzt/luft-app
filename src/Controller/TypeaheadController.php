<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Zip;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TypeaheadController extends AbstractController
{
    public function prefetchAction(): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        $data = [];

        /** @var City $city */
        foreach ($cityList as $city) {
            $data[] = ['value' => $city->getName()];
        }

        return new JsonResponse($data);
    }

    public function searchAction(Request $request, RouterInterface $router): Response
    {
        $queryString = $request->query->get('query');

        $result = [];
        $zipList = $this->getDoctrine()->getRepository(Zip::class)->findByZip($queryString);

        /** @var Zip $zip */
        foreach ($zipList as $zip) {
            $result = [
                'value' => [
                    'zip' => $zip->getZip(),
                ]
            ];
        }

        return new JsonResponse($result);
    }
}
