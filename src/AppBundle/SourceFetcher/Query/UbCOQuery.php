<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

class UbCOQuery extends AbstractQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        $this->pollutant = ['CO'];

        parent::__construct($reporting);
    }
}
