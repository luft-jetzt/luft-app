<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    protected function unpackPollutantList(array $pollutantList): array
    {
        $viewModelList = [];

        foreach ($pollutantList as $pollutant) {
            $viewModelList = array_merge($viewModelList, $pollutant);
        }

        return array_values($viewModelList);
    }
}
