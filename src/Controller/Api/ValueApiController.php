<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\DataPersister\PersisterInterface;
use App\Air\Value\Value;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    #[Route(path: '/api/value', name: 'api_value_put', methods: ['PUT'], priority: 213)]
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
