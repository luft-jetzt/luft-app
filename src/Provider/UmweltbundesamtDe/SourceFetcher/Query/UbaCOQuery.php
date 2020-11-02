<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\COFilter;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\ReportingInterface;

class UbaCOQuery extends AbstractUbaQuery
{
    /** @var int $component */
    protected $component = 2;

    /** @var array $scope */
    protected $scope = [4, 5];
}
