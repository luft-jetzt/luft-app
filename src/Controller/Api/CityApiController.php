<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Util\EntityMerger\EntityMergerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

class CityApiController extends AbstractApiController
{
    /**
     * Retrieve details about a city identified by the provided slug.
     *
     * @OA\Tag(name="City")
     * @OA\Response(
     *   response=200,
     *   description="Retrieve details about a city identified by the provided slug",
     *   @OA\Schema(
     *     type="array",
     *     @OA\Items(ref=@Model(type=App\Air\ViewModel\MeasurementViewModel::class))
     *   )
     * )
     */
    #[Route(path: '/api/{citySlug}', name: 'api_city', requirements: ['citySlug' => '^([A-Za-z-]+)$'], methods: ['GET'], priority: 208)]
    public function displayCityAction(
        SerializerInterface $serializer,
        PollutionDataFactory $pollutionDataFactory,
        string $citySlug
    ): Response {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
    }

    /**
     * Get a list of all registered cities form the databases.
     *
     * Note this endpoint will not return any pollution data.
     *
     * @OA\Tag(name="City")
     * @OA\Response(
     *   response=200,
     *   description="Returns a list of all cities",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    #[Route(path: '/api/city', name: 'api_city_all', methods: ['GET'], priority: 206)]
    public function cityAction(SerializerInterface $serializer): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        return new JsonResponse($serializer->serialize($cityList, 'json'), 200, [], true);
    }

    /**
     * Adds a new city.
     *
     * @OA\Tag(name="City")
     * @OA\RequestBody(
     *     description="Json of city data",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the new created city",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    #[Route(path: '/api/city', name: 'api_city_put', methods: ['PUT'], priority: 207)]
    public function putCityAction(Request $request, SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
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
     *
     * @OA\Tag(name="City")
     * @OA\RequestBody(
     *     description="Json of city data",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the updated station",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    #[Route(path: '/api/{citySlug}', name: 'api_city_post', requirements: ['citySlug' => '^([A-Za-z-]+)$'], methods: ['POST'], priority: 209)]
    public function postCityAction(Request $request, SerializerInterface $serializer, #[MapEntity(expr: 'repository.findOneBySlug(citySlug)')] City $city, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedCity = $serializer->deserialize($requestBody, City::class, 'json');

        $city = $entityMerger->merge($updatedCity, $city);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
    }
}
