<?php

namespace AppBundle\SourceFetcher;

use AppBundle\SourceFetcher\Query\QueryInterface;

interface SourceFetcherInterface
{
    public function query(QueryInterface $query = null): string;
}
