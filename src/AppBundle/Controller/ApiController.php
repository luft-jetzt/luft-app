<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    public function stationAction(Request $request, string $stationCode): Response
    {
        $station = $this->getDoctrine()->getRepository('AppBundle:Station')->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $boxList = $this->getPollutionDataFactory()->createDecoratedBoxList([$station]);

        return $this->render(
            'AppBundle:Default:station.html.twig',
            [
                'station' => $station,
                'boxList' => $boxList
            ]
        );
    }

    /**
     * Get pollution data for a coord by latitude and longitude.
     *
     * @ApiDoc(
     *   description="Retrieve pollution data for coords",
     *   parameters={
     *     {"name"="latitude", "dataType"="float", "required"=true, "description"="Latitude"},
     *     {"name"="longitude", "dataType"="float", "required"=true, "description"="Longitude"}
     *   }
     * )
     */
    public function displayAction(Request $request): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('AppBundle:Default:select.html.twig');
        }

        $stationList = $this->findNearestStations($coord);

        $boxList = $this->getPollutionDataFactory()->createDecoratedBoxList($stationList);

        return new JsonResponse($this->get('jms_serializer')->serialize($boxList, 'json'), 200, [], true);
    }
}
