<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class DisplayApiController extends AbstractApiController
{
    /**
     * Get pollution data for a coord by latitude and longitude or a zip code. You must either provide a coord or a zip code.
     *
     * @SWG\Tag(name="Station")
     * @SWG\Parameter(
     *     name="latitude",
     *     in="query",
     *     type="number",
     *     description="Latitude"
     * )
     * @SWG\Parameter(
     *     name="longitude",
     *     in="query",
     *     type="number",
     *     description="Longitude"
     * )
     * @SWG\Parameter(
     *     name="zip",
     *     in="query",
     *     type="number",
     *     description="Zip code"
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns pollution data of specified station",
     *   @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=App\Air\ViewModel\MeasurementViewModel::class))
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
}
