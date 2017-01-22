<?php

namespace AppBundle\SourceFetcher\Query;

class UbCOQuery extends AbstractQuery
{
    public function __construct(\DateTime $datetime)
    {
        $this->pollutant = ['CO'];
        $this->scope = ['8SMW'];
        $this->group = ['station'];

        $from = $datetime->format('U');
        $to = $from + 3600;

        $this->range = [$from, $to];
    }
}