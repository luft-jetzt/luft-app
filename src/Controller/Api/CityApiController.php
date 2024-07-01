<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Serializer\LuftSerializerInterface;
use App\Util\EntityMerger\EntityMergerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

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
    public function displayCityAction(
        LuftSerializerInterface $serializer,
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
    public function cityAction(LuftSerializerInterface $serializer): Response
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
     *     @OA\JsonContent()
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the new created city",
     *   @Model(type=App\Entity\City::class)
     * )
     */
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
     *
     * @OA\Tag(name="City")
     * @OA\RequestBody(
     *     description="Json of city data",
     *     required=true,
     *     @OA\JsonContent()
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the updated station",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    public function postCityAction(Request $request, LuftSerializerInterface $serializer, City $city, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedCity = $serializer->deserialize($requestBody, City::class, 'json');

        $city = $entityMerger->merge($updatedCity, $city);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
    }
}
