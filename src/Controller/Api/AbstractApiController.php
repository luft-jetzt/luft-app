<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractApiController extends AbstractController
{
    protected function unpackPollutantList(array $pollutantList): array
    {
        $viewModelList = [];

        foreach ($pollutantList as $pollutant) {
            $viewModelList = array_merge($viewModelList, $pollutant);
        }

        return $viewModelList;
    }

    protected function deserializeRequestBodyToArray(Request $request, SerializerInterface $serializer, string $expectedFqcn): array
    {
        $body = $request->getContent();

        if ('[' === $body[0]) {
            $type = sprintf('array<%s>', $expectedFqcn);

            $objectList = $serializer->deserialize($body, $type, 'json');
        } else {
            $object = $serializer->deserialize($body, $expectedFqcn, 'json');

            $objectList = [$object];
        }

        return $objectList;
    }
}
