<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaNO2Query extends AbstractUbaQuery
{
    protected int $component = 5;
    protected array $scope = [2, 3];
}
