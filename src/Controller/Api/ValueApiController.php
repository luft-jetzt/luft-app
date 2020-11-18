<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Pollution\DataPersister\PersisterInterface;
use App\Pollution\Value\Value;
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
    public function putValueAction(Request $request, SerializerInterface $serializer, PersisterInterface $persister): Response
    {
        $body = $request->getContent();

        dd($body);
        /** @var Value $value */
        $value = $serializer->deserialize($body, Value::class, 'json');

        $persister->persistValues([$value]);

        return new JsonResponse($serializer->serialize($value, 'json'), 200, [], true);
    }
}
