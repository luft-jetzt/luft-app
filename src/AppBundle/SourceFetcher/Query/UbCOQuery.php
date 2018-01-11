<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

class UbCOQuery extends AbstractQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        $this->pollutant = ['CO'];
        $this->scope = ['8SMW'];
        $this->group = ['station'];

        parent::__construct($reporting);
    }
}
