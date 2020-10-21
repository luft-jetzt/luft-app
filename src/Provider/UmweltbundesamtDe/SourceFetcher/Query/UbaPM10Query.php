<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaPM10Query extends AbstractUbaQuery
{
    /** @var int $component */
    protected $component = 1;

    /** @var array $scope */
    protected $scope = [1];
}
