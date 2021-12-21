<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaSO2Query extends AbstractUbaQuery
{
    protected int $component = 4;
    protected array $scope = [1, 2, 3];
}
