<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * Get pollution data for a provided station code.
     *
     * ApiDoc(
     *   section="Data",
     *   description="Retrieve pollution data for stations"
     * )
     */
    public function displayStationAction(SerializerInterface $serializer, string $stationCode, PollutionDataFactory $pollutionDataFactory): Response
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
    public function displayCityAction(SerializerInterface $serializer, PollutionDataFactory $pollutionDataFactory, string $citySlug): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

        if (!$city) {
            throw $this->createNotFoundException();
        }

        $stationList = $this->getStationListForCity($city);
        $stationsBoxList = $this->createBoxListForStationList($pollutionDataFactory, $stationList);

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
    public function displayAction(Request $request, SerializerInterface $serializer, PollutionDataFactory $pollutionDataFactory): Response
    {
        $coord = $this->getCoordByRequest($request);

        if (!$coord) {
            throw $this->createNotFoundException();
        }

        $boxList = $pollutionDataFactory->setCoord($coord)->createDecoratedBoxList();

        return new JsonResponse($serializer->serialize($boxList, 'json'), 200, [], true);
    }

    /**
     * Get details of the city identified by <code>citySlug</code>.
     *
     * Retrieve a list of all known cities by leaving <code>citySlug</code> empty.
     *
     * ApiDoc(
     *   section="City",
     *   description="Retrieve details for cities"
     * )
     */
    public function cityAction(SerializerInterface $serializer, string $citySlug = null): Response
    {
        if ($citySlug) {
            $city = $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);

            if (!$city) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($serializer->serialize($city, 'json'), 200, [], true);
        } else {
            $cityList = $this->getDoctrine()->getRepository(City::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($cityList, 'json'), 200, [], true);
    }

    /**
     * Get details of the station identified by <code>stationCode</code>.
     *
     * Retrieve a list of all known stations by leaving <code>stationCode</code> empty.
     *
     * ApiDoc(
     *   section="Station",
     *   description="Retrieve details for stations"
     * )
     */
    public function stationAction(SerializerInterface $serializer, string $stationCode = null): Response
    {
        if ($stationCode) {
            $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

            if (!$station) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($serializer->serialize($station, 'json'), 200, [], true);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }
}
