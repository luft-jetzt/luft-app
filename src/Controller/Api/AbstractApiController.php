<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Serializer\LuftSerializerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    protected function deserializeRequestBodyToArray(Request $request, LuftSerializerInterface $serializer, string $expectedFqcn): array
    {
        $body = $request->getContent();

        if ('[' === $body[0]) {
            $type = sprintf('%s[]', $expectedFqcn);

            $objectList = $serializer->deserialize($body, $type, 'json');
        } else {
            $object = $serializer->deserialize($body, $expectedFqcn, 'json');

            $objectList = [$object];
        }

        return $objectList;
    }
}
