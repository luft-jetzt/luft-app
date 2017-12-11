<?php

namespace AppBundle\SourceFetcher\Parser;

use AppBundle\SourceFetcher\Query\QueryInterface;

class LdParser implements ParserInterface
{
    protected $query = null;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function parse(string $string, string $pollutant): array
    {

    }
}
