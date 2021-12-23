<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaCOQuery extends AbstractUbaQuery
{
    protected int $component = 2;
    protected array $scope = [4, 5];
}
