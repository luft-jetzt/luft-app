<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

class UbaO3Query extends AbstractUbaQuery
{
    /** @var int $component */
    protected $component = 3;

    /** @var array $scope */
    protected $scope = [2, 3, 4, 5];
}
