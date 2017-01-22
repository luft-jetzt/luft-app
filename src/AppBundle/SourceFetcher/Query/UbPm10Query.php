<?php

namespace AppBundle\SourceFetcher\Query;

class UbPm10Query extends AbstractQuery
{
    public function __construct(\DateTime $datetime)
    {
        $this->pollutant = ['PM10'];
        $this->scope = ['1TMW'];
        $this->group = ['station'];

        $from = $datetime->format('U');
        $to = $from + 86400;

        $this->range = [$from, $to];
    }
}