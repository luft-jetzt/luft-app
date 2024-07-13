<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Geocoding\RequestConverter\RequestConverterInterface;
use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Entity\Station;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class DisplayApiController extends AbstractApiController
{
    /**
     * Get pollution data for a coord by latitude and longitude or a zip code.
     *
     * You must either provide a coord with <code>latitude</code> and <code>longitude</code> or a five digit zip code.
     */
    #[OA\Tag(name: "Display")]
    #[OA\Parameter(
        name: "latitude",
        description: "Latitude",
        in: "query",
        schema: new OA\Schema(type: "float")
    )]
    #[OA\Parameter(
        name: "longitude",
        description: "Longitude",
        in: "query",
        schema: new OA\Schema(type: "float")
    )]
    #[OA\Parameter(
        name: "zip",
        description: "Zip code",
        in: "query",
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Response(
        response: 200,
        description: "Returns pollution data of specified station",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: new Model(type: App\Air\ViewModel\MeasurementViewModel::class))
        )
    )]
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
     */
    #[OA\Tag(name: "Display")]
    #[OA\Response(
        response: 200,
        description: "Retrieve pollution data for station",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: new Model(type: App\Air\ViewModel\MeasurementViewModel::class))
        )
    )]
    #[OA\Parameter(
        name: "stationCode",
        description: "station code",
        in: "path",
        schema: new OA\Schema(type: "string")
    )]
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
