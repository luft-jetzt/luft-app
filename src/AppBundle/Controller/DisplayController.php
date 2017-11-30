<?php

namespace AppBundle\Controller;

use Caldera\GeoBasic\Coord\Coord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayController extends AbstractController
{
    public function stationAction(Request $request, string $stationCode): Response
    {
        $station = $this->getDoctrine()->getRepository('AppBundle:Station')->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $dataList = $this->getDataListFromStationList([$station]);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $this->render(
            'AppBundle:Default:station.html.twig',
            [
                'station' => $station,
                'boxList' => $boxList
            ]
        );
    }

    public function indexAction(Request $request): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            return $this->render('AppBundle:Default:select.html.twig');
        }

        $stationList = $this->findNearestStations($coord);

        if (0 === count($stationList)) {
            return $this->noStationAction($request, $coord);
        }

        $dataList = $this->getDataListFromStationList($stationList);

        $boxList = $this->getBoxListFromDataList($dataList);

        $boxList = $this->decorateBoxList($boxList);

        return $this->render(
            'AppBundle:Default:display.html.twig',
            [
                'boxList' => $boxList
            ]
        );
    }

    public function noStationAction(Request $request, Coord $coord = null): Response
    {
        if (!$coord) {
            $coord = $this->getCoordByRequest($request);
        }

        $stationList = $this->findNearestStations($coord, 1000);

        return $this->render(
            'AppBundle:Default:nostations.html.twig',
            [
                'stationList' => $stationList
            ]);
    }
}
