<?php declare(strict_types=1);

namespace App\Pollution\DataList;

use App\Entity\Data;

interface DataListInterface
{
    public function addData(Data $data): DataListInterface;
    public function hasPollutant(Data $data): bool;
    public function countPollutant(int $pollutant): int;
    public function getList(): array;
    public function reset(): DataListInterface;
}
