<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Geocoding\RequestConverter\RequestConverterInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Pollution\StationFinder\ElasticStationFinder;
use Caldera\GeoBasic\Bounds\Bounds;
use Caldera\GeoBasic\Coord\Coord;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * Get pollution data for a provided station code.
     *
     * @SWG\Tag(name="Station")
     * @SWG\Response(
     *   response=200,
     *   description="Retrieve pollution data for station",
     *   @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=App\Pollution\Box\Box::class))
     *   )
     * )
     * @SWG\Parameter(
     *     name="stationCode",
     *     in="path",
     *     type="string",
     *     description="station code"
     * )
     */
    public function displayStationAction(
        SerializerInterface $serializer,
        string $stationCode,
        PollutionDataFactory $pollutionDataFactory
    ): Response {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $pollutantList = $pollutionDataFactory->setCoord($station)->createDecoratedPollutantList();

        return new JsonResponse($serializer->serialize($this->unpackPollutantList($pollutantList), 'json'), 200, [], true);
    }

    /**
     * Get pollution data for a provided city slug.
     *
     * @SWG\Tag(name="Data")
     * @SWG\Response(
     *   response=200,
     *   description="Retrieve pollution data for cities",
     *   @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=App\Pollution\Box\Box::class))
     *   )
     * )
     */
    public function displayCityAction(
        SerializerInterface $serializer,
        PollutionDataFactory $pollutionDataFactory,
        string $citySlug
    ): Response {
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
     *     @SWG\Items(ref=@Model(type=App\Pollution\Box\Box::class))
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

    /**
     * Get details of the city identified by <code>citySlug</code>.
     *
     * Retrieve a list of all known cities by leaving <code>citySlug</code> empty.
     *
     * @SWG\Tag(name="City")
     * @SWG\Parameter(
     *     name="citySlug",
     *     in="path",
     *     type="string",
     *     description="city slug"
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns details for specified city",
     *   @Model(type=App\Entity\City::class)
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
     * Get details of the city identified by <code>citySlug</code>.
     *
     * Retrieve a list of all known cities by leaving <code>citySlug</code> empty.
     *
     * @SWG\Tag(name="Station")
     * @SWG\Parameter(
     *     name="stationCode",
     *     in="path",
     *     type="string",
     *     description="station code"
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns details for specified station",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function stationAction(Request $request, SessionInterface $session, SerializerInterface $serializer, string $stationCode = null, ElasticStationFinder $stationFinder): Response
    {
        $providerIdentifier = $request->get('provider');

        if ($request->get('remember_stations')) {
            $excludeStationIds = $this->getRememberedStations($session);
        } else {
            $excludeStationIds = [];
        }

        if ($stationCode) {
            $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

            if (!$station) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($serializer->serialize($station, 'json'), 200, [], true);
        } elseif ($request->get('north') && $request->get('east') && $request->get('south') && $request->get('west')) {
            $northWest = new Coord((float) $request->get('north'), (float) $request->get('west'));
            $southEast = new Coord((float) $request->get('south'), (float) $request->get('east'));

            $stationList = $stationFinder->findStationsInBounds(new Bounds($northWest, $southEast), null, $providerIdentifier, $excludeStationIds);
        } elseif ($providerIdentifier) {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findActiveStationsByProvider($providerIdentifier);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        if ($request->get('remember_stations')) {
            $this->rememberStations($session, $stationList);
        } else {
            $session->set('remember_stations', []);
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }

    protected function rememberStations(SessionInterface $session, array $stationList): void
    {
        $cachedStationList = $this->getRememberedStations($session);

        /** @var Station $station
         */
        foreach ($stationList as $station) {
            $cachedStationList[] = $station->getId();
        }

        $session->set('station_list', []);//$cachedStationList);
    }

    protected function getRememberedStations(SessionInterface $session): array
    {
        $list = $session->get('station_list', []);

        return $list;
    }

    protected function unpackPollutantList(array $pollutantList): array
    {
        $boxList = [];

        foreach ($pollutantList as $pollutant) {
            $boxList = array_merge($boxList, $pollutant);
        }

        return $boxList;
    }
}
