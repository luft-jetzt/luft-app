<?php

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use AppBundle\Entity\Station;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends AbstractController
{
    public function showAction(Request $request, string $citySlug): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationsBoxList = $this->createBoxListForStationList($stationList);

        return $this->render('AppBundle:City:show.html.twig', [
            'city' => $city,
            'stationList' => $stationList,
            'stationBoxList' => $stationsBoxList,
        ]);
    }
}
