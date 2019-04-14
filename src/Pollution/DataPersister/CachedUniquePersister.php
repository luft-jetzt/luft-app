<?php declare(strict_types=1);

namespace App\Pollution\DataPersister;

use App\Entity\Data;
use App\Pollution\StationCache\StationCacheInterface;
use App\Pollution\UniqueStrategy\UniqueStrategyInterface;
use App\Pollution\Value\Value;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CachedUniquePersister extends Persister implements UniquePersisterInterface
{

}
