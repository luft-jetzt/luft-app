<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Map\MapStationFactory;
use App\Repository\DataRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MapApiController extends AbstractApiController
{
    protected const array SCOPE_LIST = ['official', 'all'];

    // Deutschland-Umgriff, auf den bbox-Anfragen geklemmt werden
    protected const float LONGITUDE_MIN = 5.0;
    protected const float LONGITUDE_MAX = 16.0;
    protected const float LATITUDE_MIN = 47.0;
    protected const float LATITUDE_MAX = 56.0;

    protected const int CACHE_TTL = 300;

    #[OA\Tag(name: "Map")]
    #[OA\Get(
        parameters: [
            new OA\Parameter(
                name: "pollutant",
                description: "Schadstoff-Filter: all (Gesamtindex) oder pm10, pm25, no2, o3, so2, co, uvindex, temperature",
                in: "query",
                required: false
            ),
            new OA\Parameter(
                name: "scope",
                description: "official (nur amtliche Stationen, Default) oder all (inkl. Sensor.Community, nur mit bbox)",
                in: "query",
                required: false
            ),
            new OA\Parameter(
                name: "bbox",
                description: "Kartenausschnitt west,süd,ost,nord in Grad; Pflicht bei scope=all",
                in: "query",
                required: false
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "GeoJSON FeatureCollection of stations with their current pollution level",
            ),
            new OA\Response(
                response: 400,
                description: "Invalid parameters",
            ),
        ]
    )]
    /**
     * Returns all stations with their most recent values as GeoJSON for the map of Germany.
     */
    public function stationsAction(Request $request, DataRepository $dataRepository, MapStationFactory $mapStationFactory, CacheInterface $cache): Response
    {
        $pollutant = $request->query->get('pollutant', 'all');

        if ('all' !== $pollutant && !array_key_exists($pollutant, MapStationFactory::POLLUTANT_IDS)) {
            return $this->createErrorResponse(sprintf('Unknown pollutant "%s", allowed values are: all, %s', $pollutant, implode(', ', array_keys(MapStationFactory::POLLUTANT_IDS))));
        }

        $scope = $request->query->get('scope', 'official');

        if (!in_array($scope, self::SCOPE_LIST, true)) {
            return $this->createErrorResponse(sprintf('Unknown scope "%s", allowed values are: %s', $scope, implode(', ', self::SCOPE_LIST)));
        }

        $bboxParameter = $request->query->get('bbox');

        if ('all' === $scope && !$bboxParameter) {
            return $this->createErrorResponse('Parameter bbox is required for scope=all');
        }

        $bbox = null;

        if ($bboxParameter) {
            $bbox = $this->parseBbox($bboxParameter);

            if (!$bbox) {
                return $this->createErrorResponse('Invalid bbox, expected format is west,south,east,north within Germany');
            }
        }

        $pollutantIdentifier = 'all' === $pollutant ? null : $pollutant;
        $officialOnly = 'official' === $scope;

        if ($officialOnly && !$bbox) {
            // nur die statischen official-Varianten landen im Redis-Cache — bbox-Kombinationen nicht
            $featureCollection = $cache->get(sprintf('map-stations-official-%s', $pollutant), function (ItemInterface $item) use ($dataRepository, $mapStationFactory, $pollutantIdentifier): array {
                $item->expiresAfter(self::CACHE_TTL);

                return $this->createFeatureCollection($dataRepository, $mapStationFactory, $pollutantIdentifier, true, null);
            });
        } else {
            $featureCollection = $this->createFeatureCollection($dataRepository, $mapStationFactory, $pollutantIdentifier, $officialOnly, $bbox);
        }

        $response = new JsonResponse($featureCollection);
        $response->headers->set('Content-Type', 'application/geo+json');
        $response->setPublic();
        $response->setMaxAge(self::CACHE_TTL);

        return $response;
    }

    protected function createFeatureCollection(DataRepository $dataRepository, MapStationFactory $mapStationFactory, ?string $pollutantIdentifier, bool $officialOnly, ?array $bbox): array
    {
        $rowList = $dataRepository->findCurrentDataForMap($pollutantIdentifier, $officialOnly, $bbox);

        return $mapStationFactory->createFeatureCollection($rowList);
    }

    /**
     * Zerlegt „west,süd,ost,nord", klemmt die Werte auf den Deutschland-Umgriff und liefert
     * null für unbrauchbare Angaben (falsches Format oder leerer Ausschnitt).
     */
    protected function parseBbox(string $bboxParameter): ?array
    {
        $partList = explode(',', $bboxParameter);

        if (4 !== count($partList)) {
            return null;
        }

        $valueList = [];

        foreach ($partList as $part) {
            $part = trim($part);

            if (!is_numeric($part)) {
                return null;
            }

            $valueList[] = (float) $part;
        }

        [$west, $south, $east, $north] = $valueList;

        $west = max(self::LONGITUDE_MIN, min(self::LONGITUDE_MAX, $west));
        $east = max(self::LONGITUDE_MIN, min(self::LONGITUDE_MAX, $east));
        $south = max(self::LATITUDE_MIN, min(self::LATITUDE_MAX, $south));
        $north = max(self::LATITUDE_MIN, min(self::LATITUDE_MAX, $north));

        if ($west >= $east || $south >= $north) {
            return null;
        }

        return [$west, $south, $east, $north];
    }

    protected function createErrorResponse(string $message): JsonResponse
    {
        return new JsonResponse(['error' => $message], Response::HTTP_BAD_REQUEST);
    }
}
