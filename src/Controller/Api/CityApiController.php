<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Util\EntityMerger\EntityMergerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class CityApiController extends AbstractApiController
{
    /**
     * Retrieve pollution data for a provided city slug.
     *
     * @SWG\Tag(name="City")
     * @SWG\Response(
     *   response=200,
     *   description="Retrieve pollution data for cities",
     *   @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=App\Air\ViewModel\MeasurementViewModel::class))
     *   )
     * )
     */
    public function displayCityAction(
        SerializerInterface $serializer,
        PollutionDataFactory $pollutionDataFactory,
        string $citySlug
    ): Response {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationViewModelList = $this->createViewModelListForStationList($pollutionDataFactory, $stationList);

        return new JsonResponse($serializer->serialize($stationViewModelList, 'json'), 200, [], true);
    }

    /**
     * Get a list of all registered cities form the databases.
     *
     * Note this endpoint will not return any pollution data.
     *
     * @SWG\Tag(name="City")
     * @SWG\Response(
     *   response=200,
     *   description="Returns a list of all cities",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    public function cityAction(SerializerInterface $serializer): Response
    {
        $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();

        return new JsonResponse($serializer->serialize($cityList, 'json'), 200, [], true);
    }

    /**
     * Adds a new city.
     *
     * @SWG\Tag(name="City")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="Json of city data",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns the new created city",
     *   @Model(type=App\Entity\City::class)
     * )
     */
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
     * @SWG\Tag(name="City")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="Json of city data",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns the updated station",
     *   @Model(type=App\Entity\City::class)
     * )
     * @Entity("city", expr="repository.findOneBySlug(citySlug)")
     */
    public function postCityAction(Request $request, SerializerInterface $serializer, City $city, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedCity = $serializer->deserialize($requestBody, City::class, 'json');

        $city = $entityMerger->merge($updatedCity, $city);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
    }
}
