<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
