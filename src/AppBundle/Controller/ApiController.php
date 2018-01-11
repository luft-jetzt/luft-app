<?php

namespace AppBundle\Controller;

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
        $station = $this->getDoctrine()->getRepository('AppBundle:Station')->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $this->getPollutionDataFactory()->setCoord($station)->createDecoratedBoxList();

        return new JsonResponse($this->get('jms_serializer')->serialize($boxList, 'json'), 200, [], true);
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
