<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Station;
use App\Util\EntityMerger\EntityMergerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class StationApiController extends AbstractApiController
{
    /**
     * Returns details of a specified station.
     *
     * Get details of the station identified by <code>stationCode</code>. Note this will not return any pollution data.
     *
     * @OA\Tag(name="Station")
     * @OA\Parameter(
     *     name="stationCode",
     *     in="path",
     *     description="station code",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
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
     * @OA\Tag(name="Station")
     * @OA\Parameter(
     *     name="provider",
     *     in="query",
     *     description="Provider identifier",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns a list of all known stations",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function listStationAction(Request $request, SerializerInterface $serializer): Response
    {
        $providerIdentifier = $request->get('provider');

        if ($providerIdentifier) {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findStationsByProvider($providerIdentifier);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }

    /**
     * Add a new station.
     *
     * @OA\Tag(name="Station")
     * @OA\RequestBody(
     *     description="Json of station data",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the new created station",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function putStationAction(Request $request, SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {
        $body = $request->getContent();

        $stationList = $this->deserializeRequestBodyToArray($request, $serializer, Station::class);

        try {
            $this->persistStationList($managerRegistry, $stationList);

            if (1 === count($stationList)) {
                $result = array_pop($stationList);
            } else {
                $result = $stationList;
            }

            return new JsonResponse($serializer->serialize($result, 'json'), Response::HTTP_OK, [], true);
        } catch (UniqueConstraintViolationException) {
            return new JsonResponse($serializer->serialize([
                'status' => 'error',
                'message' => 'This record already exists',
            ], 'json'), Response::HTTP_CONFLICT, [], true);
        }

    }

    protected function persistStationList(ManagerRegistry $managerRegistry, array $stationList): array
    {
        $em = $managerRegistry->getManager();

        /** @var Station $station */
        foreach ($stationList as $station) {
            if ($station->getLatitude() && $station->getLongitude()) {
                $em->persist($station);
            }
        }

        $em->flush();

        return $stationList;
    }

    /**
     * Updates station data.
     *
     * @OA\Tag(name="Station")
     * @OA\RequestBody(
     *     description="Json of station data",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns the updated station",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function postStationAction(Request $request, SerializerInterface $serializer, #[MapEntity(expr: 'repository.findOneByStationCode(stationCode)')] Station $station, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedStation = $serializer->deserialize($requestBody, Station::class, 'json');

        $station = $entityMerger->merge($updatedStation, $station);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($station, 'json'), 200, [], true);
    }
}
