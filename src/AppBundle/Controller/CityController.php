<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

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

    public function twitterAction(Request $request, string $citySlug): Response
    {
        /** @var City $city */
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city || $city->getTwitterUsername()) {
            throw $this->createNotFoundException();
        }

        $this->getSession()->set('twitterCity', $city);

        return $this->render('AppBundle:City:twitter.html.twig', [
            'city' => $city,
        ]);
    }

    protected function getSession(): Session
    {
        /** @var Session $session */
        $session = $this->get('session');

        return $session;
    }
}
