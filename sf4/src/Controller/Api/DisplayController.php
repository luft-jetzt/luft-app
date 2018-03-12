<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends AbstractController
{
    /**
     * Get pollution data for a provided station code.
     *
     * ApiDoc(
     *   section="Data",
     *   description="Retrieve pollution data for stations"
     * )
     */
    public function stationAction(Serializer $serializer, string $stationCode, PollutionDataFactory $pollutionDataFactory): Response
    {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory->setCoord($station)->createDecoratedBoxList();

        return new JsonResponse($serializer->serialize($boxList, 'json'), 200, [], true);
    }

    /**
     * Get pollution data for a provided city slug.
     *
     * ApiDoc(
     *   section="Data",
     *   description="Retrieve pollution data for cities"
     * )
     */
    public function cityAction(Serializer $serializer, string $citySlug): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationsBoxList = $this->createBoxListForStationList($stationList);

        return new JsonResponse($serializer->serialize($stationsBoxList, 'json'), 200, [], true);
    }

    /**
     * Get pollution data for a coord by latitude and longitude or a zip code. You must either provide a coord or a zip code.
     *
     * ApiDoc(
     *   section="Data",
     *   description="Retrieve pollution data for coords",
     *   parameters={
     *     {"name"="latitude", "dataType"="float", "required"=false, "description"="Latitude"},
     *     {"name"="longitude", "dataType"="float", "required"=false, "description"="Longitude"},
     *     {"name"="zip", "dataType"="integer", "required"=false, "description"="Zip code"}
     *   }
     * )
     */
    public function displayAction(Request $request, Serializer $serializer, PollutionDataFactory $pollutionDataFactory): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

        return new JsonResponse($serializer->serialize($boxList, 'json'), 200, [], true);
    }
}
