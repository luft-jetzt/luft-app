<?php declare(strict_types=1);

namespace App\Pollution\PollutantFactoryStrategy;

use App\Entity\Data;
use App\Pollution\DataList\DataListInterface;

interface PollutantFactoryStrategyInterface
{
    public function getMissingPollutants(DataListInterface $dataList): array;
    public function addDataToList(DataListInterface $dataList, Data $data): bool;
}
