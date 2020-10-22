<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Data;
use App\Entity\Station;
use App\Model\Data as DataModel;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class DataApiController extends AbstractApiController
{
    /**
     * Returns details of a specified station.
     *
     * Get details of the station identified by <code>stationCode</code>. Note this will not return any pollution data.
     *
     * @SWG\Tag(name="Data")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="data value",
     *     @SWG\Schema(type="string")
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Returns details for specified station",
     *   @Model(type=App\Entity\Station::class)
     * )
     */
    public function putDataAction(Request $request, SerializerInterface $serializer, ManagerRegistry $managerRegistry): Response
    {
        $body = $request->getContent();

        /** @var DataModel $dataModel */
        $dataModel = $serializer->deserialize($body, DataModel::class, 'json');

        $data = $this->convertToOrmData($managerRegistry, $dataModel);

        $em = $managerRegistry->getManager();
        $em->persist($data);
        $em->flush();
        
        return new JsonResponse($serializer->serialize($data, 'json'), 200, [], true);
    }

    protected function convertToOrmData(ManagerRegistry $managerRegistry, DataModel $dataModel): Data
    {
        $station = $managerRegistry->getRepository(Station::class)->findOneByStationCode($dataModel->getStationCode());

        $data = new Data();
        $data
            ->setDateTime($dataModel->getDateTime())
            ->setPollutant($dataModel->getPollutant())
            ->setValue($dataModel->getValue())
            ->setStation($station);

        return $data;
    }
}
