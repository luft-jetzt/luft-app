<?php declare(strict_types=1);

namespace App\Pollution\DataTableManager;

use App\Entity\Data;
use Doctrine\Persistence\ManagerRegistry;

class DataTableManager implements DataTableManagerInterface
{
    protected ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->dataManager = $managerRegistry->getManager('data');
    }

    public function saveData(Data $data): bool
    {
        $metadata = $this->dataManager->getClassMetadata(Data::class);
        $metadata->getTableName();
        $this->dataManager->persist($data);
        $this->dataManager->flush();
    }

    public function queryDataForPollutantAndDateTime(int $pollutantId, \DateTime $dateTime): ?Data
    {
        return null;
    }
}