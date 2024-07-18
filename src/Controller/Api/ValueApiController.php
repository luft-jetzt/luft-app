<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\DataPersister\PersisterInterface;
use App\Air\Value\Value;
use OpenApi\Attributes as OA;
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
    )]
    /**
     * Add values of stations.
     */
    public function putValueAction(Request $request, PersisterInterface $persister): Response
    {
        $valueList = $this->deserializeRequestBodyToArray($request, Value::class);

        $persister->persistValues($valueList);

        if (1 === count($valueList)) {
            $result = array_pop($valueList);
        } else {
            $result = $valueList;
        }

        return new JsonResponse($this->serializer->serialize($result), Response::HTTP_OK, [], true);
    }
}
