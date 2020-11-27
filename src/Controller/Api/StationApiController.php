<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Station;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Pollution\Value\Value;
use App\Util\EntityMerger\EntityMergerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
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
     *     name="provider",
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
        $providerIdentifier = $request->get('provider');

        if ($providerIdentifier) {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findActiveStationsByProvider($providerIdentifier);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($serializer->serialize($stationList, 'json'), 200, [], true);
    }

    /**
     * Add a new station.
     *
     * @SWG\Tag(name="Station")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="Json of station data",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Response(
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
        } catch (UniqueConstraintViolationException $exception) {
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
            $em->persist($station);
        }

        $em->flush();

        return $stationList;
    }

    /**
     * Updates station data.
     *
     * @SWG\Tag(name="Station")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="Json of station data",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns the updated station",
     *   @Model(type=App\Entity\Station::class)
     * )
     * @Entity("station", expr="repository.findOneByStationCode(stationCode)")
     */
    public function postStationAction(Request $request, SerializerInterface $serializer, Station $station, EntityMergerInterface $entityMerger, ManagerRegistry $managerRegistry): Response
    {
        $requestBody = $request->getContent();

        $updatedStation = $serializer->deserialize($requestBody, Station::class, 'json');

        $station = $entityMerger->merge($updatedStation, $station);

        $managerRegistry->getManager()->flush();

        return new JsonResponse($serializer->serialize($station, 'json'), 200, [], true);
    }
}
