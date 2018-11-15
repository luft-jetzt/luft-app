<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Query;

use App\Provider\UmweltbundesamtDe\SourceFetcher\Filter\COFilter;
use App\Provider\UmweltbundesamtDe\SourceFetcher\Reporting\ReportingInterface;

class UbaCOQuery extends AbstractUbaQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        parent::__construct($reporting);

        $this->filter = new COFilter();
    }
}
