<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
use App\Producer\Value\ValueProducerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class ValueApiController extends AbstractApiController
{
    /**
     * Add values of stations.
     *
     * @SWG\Tag(name="Value")
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
     *   @Model(type=App\Entity\Data::class)
     * )
     */
    public function putValueAction(Request $request, SerializerInterface $serializer, ValueProducerInterface $producer): Response
    {
        $valueList = $this->deserializeRequestBodyToArray($request, $serializer, Value::class);

        $producer->publishValues($valueList);

        if (1 === count($valueList)) {
            $result = array_pop($valueList);
        } else {
            $result = $valueList;
        }

        return new JsonResponse($serializer->serialize($result, 'json'), Response::HTTP_OK, [], true);
    }
}
