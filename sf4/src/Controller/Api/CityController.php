<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\City;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends AbstractController
{
    /**
     * Get details of the city identified by <code>citySlug</code>.
     *
     * Retrieve a list of all known cities by leaving <code>citySlug</code> empty.
     *
     * ApiDoc(
     *   section="City",
     *   description="Retrieve details for cities"
     * )
     */
    public function cityAction(Serializer $serializer, string $citySlug = null): Response
    {
        if ($citySlug) {
            $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

            if (!$city) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
        } else {
            $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($cityList, 'json'), 200, [], true);
    }
}
