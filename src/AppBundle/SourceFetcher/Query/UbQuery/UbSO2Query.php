<?php

namespace AppBundle\SourceFetcher\Query\UbQuery;

class UbSO2Query extends AbstractUbQuery
{
    public function __construct(\DateTimeInterface $datetime)
    {
        $this->pollutant = ['SO2'];
        $this->scope = ['1SMW'];
        $this->group = ['station'];

        $to = $datetime->format('U');
        $from = $to - 3600;

        $this->range = [$from, $to];
    }
}
