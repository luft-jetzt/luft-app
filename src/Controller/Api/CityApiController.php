<?php declare(strict_types=1);

namespace App\Controller\Api;

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
     * Get pollution data for a provided city slug.
     *
     * @SWG\Tag(name="Data")
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
     * Get details of the city identified by <code>citySlug</code>.
     *
     * Retrieve a list of all known cities by leaving <code>citySlug</code> empty.
     *
     * @SWG\Tag(name="City")
     * @SWG\Parameter(
     *     name="citySlug",
     *     in="path",
     *     type="string",
     *     description="city slug"
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns details for specified city",
     *   @Model(type=App\Entity\City::class)
     * )
     */
    public function cityAction(SerializerInterface $serializer, string $citySlug = null): Response
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
