<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Data;
use App\Entity\Station;
use Caldera\LuftApiBundle\Serializer\LuftSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AnalysisApiController extends AbstractApiController
{
    public function stationAnalysisAction(
        LuftSerializerInterface $serializer,
        string $stationCode
    ): Response {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $valueList = $this->getDoctrine()->getRepository(Data::class)->findForAnalysis($station, 1);

        return new JsonResponse($serializer->serialize($valueList, 'json'), 200, [], true);
    }
}
