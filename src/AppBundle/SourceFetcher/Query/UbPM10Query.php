<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Query;

use AppBundle\SourceFetcher\Reporting\ReportingInterface;

class UbPM10Query extends AbstractQuery
{
    public function __construct(ReportingInterface $reporting)
    {
        $this->pollutant = ['PM10'];
        $this->scope = ['1TMW'];

        parent::__construct($reporting);
    }

    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
