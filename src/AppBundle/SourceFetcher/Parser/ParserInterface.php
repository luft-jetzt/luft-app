<?php declare(strict_types=1);

namespace AppBundle\SourceFetcher\Parser;

interface ParserInterface
{
    public function parse(string $string, string $pollutant): array;
}
