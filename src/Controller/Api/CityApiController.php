<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Util\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class CityApiController extends AbstractApiController
{
    /**
     * Retrieve details about a city identified by the provided slug.
     */
    #[OA\Tag(name: "City")]
    #[OA\Response(
        response: 200,
        description: "Retrieve details about a city identified by the provided slug",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items()
        )
    )]
    public function displayCityAction(string $citySlug): Response {
        $city = $this->managerRegistry->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse($this->serializer->serialize($city, 'json'), Response::HTTP_OK, [], true);
    }

    /**
     * Get a list of all registered cities from the databases.
     *
     * Note this endpoint will not return any pollution data.
     */
    #[OA\Tag(name: "City")]
    #[OA\Response(
        response: 200,
        description: "Returns a list of all cities",
    )]
    public function cityAction(): Response
    {
        $cityList = $this->managerRegistry->getRepository(City::class)->findAll();

        return new JsonResponse($this->serializer->serialize($cityList, 'json'), 200, [], true);
    }

    /**
     * Adds a new city.
     */
    #[OA\Tag(name: "City")]
    #[OA\RequestBody(
        description: "Json of city data",
        content: new OA\JsonContent()
    )]
    #[OA\Response(
        response: 200,
        description: "Returns the newly created city",
    )]
    public function putCityAction(Request $request): Response
    {
        $requestBody = $request->getContent();

        $city = $this->serializer->deserialize($requestBody, City::class, 'json');

        $em = $this->managerRegistry->getManager();
        $em->persist($city);
        $em->flush();

        return new JsonResponse($this->serializer->serialize($city, 'json'), Response::HTTP_OK, [], true);
    }

    /**
     * Updates city data.
     */
    #[OA\Tag(name: "City")]
    #[OA\RequestBody(
        description: "Json of city data",
        required: true,
        content: new OA\JsonContent()
    )]
    #[OA\Response(
        response: 200,
        description: "Returns the updated city",
    )]
    #[Entity('city', expr: 'repository.findOneBySlug(citySlug)')]
    public function postCityAction(Request $request, City $city, EntityMergerInterface $entityMerger): Response
    {
        $requestBody = $request->getContent();

        $updatedCity = $this->serializer->deserialize($requestBody, City::class);

        $city = $entityMerger->merge($updatedCity, $city);

        $this->managerRegistry->getManager()->flush();

        return new JsonResponse($this->serializer->serialize($city), Response::HTTP_OK, [], true);
    }
}
