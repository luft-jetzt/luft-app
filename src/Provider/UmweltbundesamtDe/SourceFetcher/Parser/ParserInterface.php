<?php declare(strict_types=1);

namespace App\Provider\UmweltbundesamtDe\SourceFetcher\Parser;

interface ParserInterface
{
    public function parse(array $string, int $pollutant): array;
}
