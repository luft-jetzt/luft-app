<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Air\Serializer\LuftSerializerInterface;
use App\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        protected readonly LuftSerializerInterface $serializer,
        ManagerRegistry $managerRegistry
    )
    {
        parent::__construct($managerRegistry);
    }

    protected function unpackPollutantList(array $pollutantList): array
    {
        $viewModelList = [];

        foreach ($pollutantList as $pollutant) {
            $viewModelList = array_merge($viewModelList, $pollutant);
        }

        return $viewModelList;
    }

    protected function deserializeRequestBodyToArray(Request $request, string $expectedFqcn): array
    {
        $body = $request->getContent();

        if ('' === $body) {
            throw new \InvalidArgumentException('Request body is empty');
        }

        if ('[' === $body[0]) {
            $type = sprintf('%s[]', $expectedFqcn);

            $objectList = $this->serializer->deserialize($body, $type);
        } else {
            $object = $this->serializer->deserialize($body, $expectedFqcn);

            $objectList = [$object];
        }

        return $objectList;
    }
}
