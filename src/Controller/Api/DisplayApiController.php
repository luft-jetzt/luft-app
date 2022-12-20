<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Station;
use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

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
     *     type="number",
     *     description="Latitude"
     * )
     * @OA\Parameter(
     *     name="longitude",
     *     in="query",
     *     type="number",
     *     description="Longitude"
     * )
     * @OA\Parameter(
     *     name="zip",
     *     in="query",
     *     type="number",
     *     description="Zip code"
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
     *     type="string",
     *     description="station code"
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
