<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Data;
use App\Entity\Station;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AnalysisApiController extends AbstractApiController
{
    public function stationAnalysisAction(string $stationCode): Response {
        $station = $this->managerRegistry->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $valueList = $this->managerRegistry->getRepository(Data::class)->findForAnalysis($station, 1);

        return new JsonResponse($this->serializer->serialize($valueList, 'json'), Response::HTTP_OK, [], true);
    }
}
