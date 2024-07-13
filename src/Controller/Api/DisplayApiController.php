<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Entity\Station;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayApiController extends AbstractApiController
{
    /**
     * Get pollution data for a coord by latitude and longitude or a zip code.
     *
     * You must either provide a coord with <code>latitude</code> and <code>longitude</code> or a five digit zip code.
     *
     * @OA\Tag(name="Display")
     * @OA\Parameter(
     *     name="latitude",
     *     in="query",
     *     description="Latitude",
     *     @OA\Schema(type="float")
     * )
     * @OA\Parameter(
     *     name="longitude",
     *     in="query",
     *     description="Longitude",
     *     @OA\Schema(type="float")
     * )
     * @OA\Parameter(
     *     name="zip",
     *     in="query",
     *     description="Zip code",
     *     @OA\Schema(type="number")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns pollution data of specified station",
     *   @OA\Schema(
     *     type="array",
     *     @OA\Items(ref=@Model(type=App\Air\ViewModel\MeasurementViewModel::class))
     *   )
     * )
     */
    public function displayAction(
        Request $request,
        SerializerInterface $serializer,
        PollutionDataFactory $pollutionDataFactory,
        RequestConverterInterface $requestConverter
    ): Response {
        $coord = $requestConverter->getCoordByRequest($request);

        if (!$coord) {
            throw $this->createNotFoundException();
        }

        $pollutantList = $pollutionDataFactory->setCoord($coord)->createDecoratedPollutantList();

        return new JsonResponse($serializer->serialize($this->unpackPollutantList($pollutantList), 'json'), 200, [], true);
    }


    /**
     * Get pollution data for a provided station code.
     *
     * @OA\Tag(name="Display")
     * @OA\Response(
     *   response=200,
     *   description="Retrieve pollution data for station",
     *   @OA\Schema(
     *     type="array",
     *     @OA\Items(ref=@Model(type=App\Air\ViewModel\MeasurementViewModel::class))
     *   )
     * )
     * @OA\Parameter(
     *     name="stationCode",
     *     in="path",
     *     description="station code",
     *     @OA\Schema(type="string")
     * )
     */
    public function displayStationAction(
        SerializerInterface $serializer,
        string $stationCode,
        PollutionDataFactory $pollutionDataFactory
    ): Response {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $pollutantList = $pollutionDataFactory->setStation($station)->createDecoratedPollutantList();

        return new JsonResponse($serializer->serialize($this->unpackPollutantList($pollutantList), 'json'), 200, [], true);
    }
}
