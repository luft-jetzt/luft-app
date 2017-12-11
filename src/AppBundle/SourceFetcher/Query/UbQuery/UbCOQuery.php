<?php

namespace AppBundle\SourceFetcher\Query\UbQuery;

class UbCOQuery extends AbstractUbQuery
{
    public function __construct(\DateTimeInterface $datetime)
    {
        $this->pollutant = ['CO'];
        $this->scope = ['8SMW'];
        $this->group = ['station'];

        $to = $datetime->format('U');
        $from = $to - 3600;

        $this->range = [$from, $to];
    }
}
