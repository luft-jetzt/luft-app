<?php declare(strict_types=1);

namespace App\DataPurger;

use App\Entity\Data;
use App\Provider\ProviderInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;

interface DataPurgerInterface
{
    public function purge(\DateTime $untilDateTime, ProviderInterface $provider, bool $withTags): int;
    public function purgeData(\DateTime $untilDateTime, ProviderInterface $provider, bool $withTags): void;
    public function countData(\DateTime $untilDateTime, ProviderInterface $provider, bool $withTags): int;
}
