<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

class UbSO2Query extends AbstractQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        $this->pollutant = ['SO2'];
        $this->scope = ['1SMW'];

        parent::__construct($reporting);
    }
}
