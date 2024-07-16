<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\PollutionDataFactory\PollutionDataFactory;
use App\Entity\Data;
use App\Entity\Station;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnalysisApiController extends AbstractApiController
{
    #[Route(path: '/api/{stationCode}/analysis', name: 'api_station_analysis', requirements: ['stationCode' => '^([A-Z]{4,6})([0-9]{1,5})$'], methods: ['GET'], priority: 211)]
    public function stationAnalysisAction(
        SerializerInterface $serializer,
        string $stationCode,
        PollutionDataFactory $pollutionDataFactory
    ): Response {
        $station = $this->getDoctrine()->getRepository(Station::class)->findOneByStationCode($stationCode);

        if (!$station) {
            throw $this->createNotFoundException();
        }

        $valueList = $this->getDoctrine()->getRepository(Data::class)->findForAnalysis($station, 1);

        return new JsonResponse($serializer->serialize($valueList, 'json'), 200, [], true);
    }
}
