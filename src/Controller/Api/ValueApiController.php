<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ValueApiController extends AbstractApiController
{
    /**
     * Add values of stations.
     *
     * @OA\Tag(name="Value")
     * @OA\RequestBody(
     *     required=true,
     *     description="data value",
     *     @OA\JsonContent(
     *      example={
     *       "station_code": "DENI200",
     *       "pollutant": "CO",
     *       "date_time": 123456,
     *       "value": 4.2
     *     }
     *   )
     * )
     * @OA\Response(
     *   response=200,
     *   description="Returns details for specified station",
     *   @Model(type=App\Entity\Data::class)
     * )
     */
    public function putValueAction(Request $request, SerializerInterface $serializer, PersisterInterface $persister): Response
    {
        $valueList = $this->deserializeRequestBodyToArray($request, $serializer, Value::class);

        $persister->persistValues($valueList);

        if (1 === count($valueList)) {
            $result = array_pop($valueList);
        } else {
            $result = $valueList;
        }

        return new JsonResponse($serializer->serialize($result, 'json'), Response::HTTP_OK, [], true);
    }
}
