<?php

namespace AppBundle\SourceFetcher\Query;

class UbSO2Query extends AbstractQuery
{
    public function __construct(\DateTime $datetime)
    {
        $this->pollutant = ['SO2'];
        $this->scope = ['1SMW'];
        $this->group = ['station'];

        $to = $datetime->format('U');
        $from = $to - 3600;

        $this->range = [$from, $to];
    }
}
