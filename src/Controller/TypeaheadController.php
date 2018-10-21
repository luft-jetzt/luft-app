<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TypeaheadController extends AbstractController
{
    public function prefetchAction(): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        $data = [];

        /** @var City $city */
        foreach ($cityList as $city) {
            $data[] = $city->getName();
        }

        return new JsonResponse($data);
    }
}
