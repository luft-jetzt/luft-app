<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Provider\ProviderInterface;

interface PersisterInterface
{
    public function persistValues(array $values): PersisterInterface;
    public function getNewValueList(): array;
    public function setProvider(ProviderInterface $provider): PersisterInterface;
    public function reset(): PersisterInterface;
}
