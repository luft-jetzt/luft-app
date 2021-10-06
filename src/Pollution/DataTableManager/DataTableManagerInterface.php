<?php declare(strict_types=1);

namespace App\Pollution\DataTableManager;

use App\Entity\Data;

interface DataTableManagerInterface
{
    public function saveData(Data $data): bool;
    public function queryDataForPollutantAndDateTime(int $pollutantId, \DateTime $dateTime): ?Data;
}
