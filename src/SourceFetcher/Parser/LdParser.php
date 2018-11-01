<?php declare(strict_types=1);

namespace App\SourceFetcher\Parser;

use App\SourceFetcher\Query\QueryInterface;

class LdParser implements ParserInterface
{
    protected $query = null;

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function parse(string $string, int $pollutant): array
    {
        // TODO: Implement parse() method.

        return [];
    }
}
