<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use FOS\ElasticaBundle\Finder\FinderInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class StationApiController extends AbstractApiController
{
    /**
     * Returns details of a specified station.
     *
     * Get details of the station identified by <code>stationCode</code>. Note this will not return any pollution data.
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
    public function stationAction(Request $request, SerializerInterface $serializer, string $stationCode = null): Response
    {
        $providerIdentifier = $request->get('provider');

        if ($stationCode) {
            $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

            if (!$station) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($serializer->serialize($station, 'json'), 200, [], true);
        } elseif ($providerIdentifier) {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findActiveStationsByProvider($providerIdentifier);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }

    /**
     * List all known stations. You may limit the list by specifing a provider identifier.
     *
     * Possible provider identifiers are:
     *
     * <ul>
     * <li><code>uba_de</code>: Umweltbundesamt</li>
     * <li><code>ld</code>: Luftdaten.info</li>
     * <li><code>hqc</code>: HQCasanova</li>
     * <li><code>owm</code>: OpenWeatherMap</li>
     * </ul>
     *
     * @SWG\Tag(name="Station")
     * @SWG\Parameter(
     *     name="provider_identifier",
     *     in="query",
     *     type="string",
     *     description="Provider identifier"
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns a list of all known stations",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function listStationAction(Request $request, SerializerInterface $serializer): Response
    {
        $north = $request->get('north');
        $south = $request->get('south');
        $west = $request->get('west');
        $east = $request->get('east');

        $providerIdentifier = $request->get('provider_identifier');

        if ($north && $west && $south && $east) {
            /** @var FinderInterface $finder */
            $finder = $this->get('fos_elastica.finder.air_station.station');

            $boolQuery = new \Elastica\Query\BoolQuery();

            $geoQuery = new \Elastica\Query\GeoBoundingBox('pin', [
                ['lon' => $west, 'lat' => $north,],
                ['lon' => $east, 'lat' => $south,],
            ]);

            $boolQuery->addMust($geoQuery);

            $boolQuery->addMust(new \Elastica\Query\Term(['provider' => $providerIdentifier]));

            $query = new \Elastica\Query($boolQuery);

            $stationList = $finder->find($query, 5000);
        } elseif ($providerIdentifier) {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findActiveStationsByProvider($providerIdentifier, 5000);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }
}
