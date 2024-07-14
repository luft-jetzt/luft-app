<?php declare(strict_types=1);

namespace App\Air\PollutantFactoryStrategy;

use App\Air\DataList\DataListInterface;
use App\Entity\Data;

interface PollutantFactoryStrategyInterface
{
    public function getMissingPollutants(DataListInterface $dataList): array;
    public function isSatisfied(DataListInterface $dataList, int $pollutantId): bool;
    public function accepts(DataListInterface $dataList, Data $data = null): bool;
    public function addDataToList(DataListInterface $dataList, Data $data = null): bool;
}
