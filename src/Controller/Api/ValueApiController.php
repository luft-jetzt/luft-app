<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\DataPersister\PersisterInterface;
use App\Air\Value\Value;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use App\Air\Serializer\LuftSerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ValueApiController extends AbstractApiController
{
    #[OA\Tag(name: "Value")]
    #[OA\RequestBody(
        description: "data value",
        required: true,
        content: new OA\JsonContent(
            example: [
                "station_code" => "DENI200",
                "pollutant" => "CO",
                "date_time" => 123456,
                "value" => 4.2
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Returns details for specified station",
        content: new Model(type: App\Entity\Data::class)
    )]
    /**
     * Add values of stations.
     */
    public function putValueAction(Request $request, LuftSerializerInterface $serializer, PersisterInterface $persister): Response
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
