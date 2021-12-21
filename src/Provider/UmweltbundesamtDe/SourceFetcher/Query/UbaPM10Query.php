<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaPM10Query extends AbstractUbaQuery
{
    protected int $component = 1;
    protected array $scope = [1];
}
