<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

interface PersisterInterface
{
    public function persistValues(array $values): PersisterInterface;
    public function getNewValueList(): array;
    public function reset(): PersisterInterface;
}
