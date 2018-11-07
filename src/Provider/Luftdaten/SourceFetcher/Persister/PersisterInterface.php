<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\Persister;

use App\Entity\Data;
use App\Entity\Station;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Value\Value;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

interface PersisterInterface
{
    public function persistValues(array $values): PersisterInterface;
    public function getNewValueList(): array;
}
