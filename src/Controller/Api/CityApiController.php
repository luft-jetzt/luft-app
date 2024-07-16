<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Util\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use Caldera\LuftApiBundle\Serializer\LuftSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
    public function displayCityAction(
        LuftSerializerInterface $serializer,
        string $citySlug
    ): Response {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
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
    public function cityAction(LuftSerializerInterface $serializer): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        return new JsonResponse($serializer->serialize($cityList, 'json'), 200, [], true);
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
    public function putCityAction(Request $request, LuftSerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $city = $serializer->deserialize($requestBody, City::class, 'json');

        $em = $managerRegistry->getManager();
        $em->persist($city);
        $em->flush();

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
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
    public function postCityAction(Request $request, LuftSerializerInterface $serializer, City $city, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedCity = $serializer->deserialize($requestBody, City::class, 'json');

        $city = $entityMerger->merge($updatedCity, $city);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
    }
}
