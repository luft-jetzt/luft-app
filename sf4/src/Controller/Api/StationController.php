<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\Station;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StationController extends AbstractController
{
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
    public function stationAction(Request $request, string $stationCode = null): Response
    {
        if ($stationCode) {
            $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

            if (!$station) {
                throw $this->createNotFoundException();
            }

            return new JsonResponse($this->get('jms_serializer')->serialize($station, 'json'), 200, [], true);
        } else {
            $stationList = $this->getDoctrine()->getRepository(Station::class)->findAll();
        }

        return new JsonResponse($this->get('jms_serializer')->serialize($stationList, 'json'), 200, [], true);

    }

}
