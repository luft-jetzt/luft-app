<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Station;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * Get pollution data for a provided station code.
     *
     * @ApiDoc(
     *   description="Retrieve pollution data for stations"
     * )
     */
    public function stationAction(Request $request, string $stationCode): Response
    {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $this->getPollutionDataFactory()->setCoord($station)->createDecoratedBoxList();

        return new JsonResponse($this->get('jms_serializer')->serialize($boxList, 'json'), 200, [], true);
    }

    /**
     * Get pollution data for a provided city slug.
     *
     * @ApiDoc(
     *   description="Retrieve pollution data for cities"
     * )
     */
    public function cityAction(Request $request, string $citySlug): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationsBoxList = $this->createBoxListForStationList($stationList);

        return new JsonResponse($this->get('jms_serializer')->serialize($stationsBoxList, 'json'), 200, [], true);
    }

    /**
     * Get pollution data for a coord by latitude and longitude or a zip code. You must either provide a coord or a zip code.
     *
     * @ApiDoc(
     *   description="Retrieve pollution data for coords",
     *   parameters={
     *     {"name"="latitude", "dataType"="float", "required"=false, "description"="Latitude"},
     *     {"name"="longitude", "dataType"="float", "required"=false, "description"="Longitude"},
     *     {"name"="zip", "dataType"="integer", "required"=false, "description"="Zip code"}
     *   }
     * )
     */
    public function displayAction(Request $request): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            throw $this->createNotFoundException();
        }

        $boxList = $this->getPollutionDataFactory()->setCoord($coord)->createDecoratedBoxList();

        return new JsonResponse($this->get('jms_serializer')->serialize($boxList, 'json'), 200, [], true);
    }
}
