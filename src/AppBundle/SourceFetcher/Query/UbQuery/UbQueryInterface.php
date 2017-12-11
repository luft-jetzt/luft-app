<?php

namespace AppBundle\SourceFetcher\Query\UbQuery;

use AppBundle\SourceFetcher\Query\QueryInterface;

interface UbQueryInterface extends QueryInterface
{
    public function getQueryString(): string;
    public function getQueryOptions(): array;
    public function getDateTimeFormat(): string;
}
