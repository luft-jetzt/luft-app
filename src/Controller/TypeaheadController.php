<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TypeaheadController extends AbstractController
{
    public function prefetchAction(): Response
    {
        return new JsonResponse(['foo', 'bar', 'baz']);
    }
}
