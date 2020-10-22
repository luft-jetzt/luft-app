<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaSO2Query extends AbstractUbaQuery
{
    /** @var int $component */
    protected $component = 4;

    /** @var array $scope */
    protected $scope = [1, 2, 3];
}
