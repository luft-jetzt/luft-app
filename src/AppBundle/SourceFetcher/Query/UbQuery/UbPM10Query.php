<?php

namespace AppBundle\SourceFetcher\Query\UbQuery;

class UbPM10Query extends AbstractUbQuery
{
    public function __construct(\DateTimeInterface $datetime)
    {
        $this->pollutant = ['PM10'];
        $this->scope = ['1TMW'];
        $this->group = ['station'];

        $to = $datetime->format('U');
        $from = $to - 86400;

        $this->range = [$from, $to];
    }

    public function getDateTimeFormat(): string
    {
        return 'd.m.Y';
    }
}
