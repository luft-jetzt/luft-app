<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaO3Query extends AbstractUbaQuery
{
    protected int $component = 3;
    protected array $scope = [2, 3, 4, 5];
}
