<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaNO2Query extends AbstractUbaQuery
{
    /** @var int $component */
    protected $component = 5;

    /** @var array $scope */
    protected $scope = [2, 3];
}
