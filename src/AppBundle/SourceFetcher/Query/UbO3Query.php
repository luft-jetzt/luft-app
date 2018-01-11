<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

class UbO3Query extends AbstractQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        $this->pollutant = ['O3'];
        $this->scope = ['1SMW'];
        $this->group = ['station'];

        parent::__construct($reporting);
    }
}
