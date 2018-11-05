<?php declare(strict_types=1);

namespace App\Pollution\DataList;

use App\Entity\Data;

interface DataListInterface
{
    public function addData(Data $data, bool $overwrite = false): DataListInterface;
    public function hasPollutant(Data $data): bool;
    public function getMissingPollutants(): array;
    public function getList(): array;
    public function reset(): DataListInterface;
}
