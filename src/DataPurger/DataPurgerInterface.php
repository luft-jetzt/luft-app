<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Entity\Data;
use App\Provider\ProviderInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

interface DataPurgerInterface
{
    public function purge(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider): int;
    public function purgeData(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider): void;
    public function countData(\DateTime $untilDateTime, bool $withTags, ProviderInterface $provider): int;
}
