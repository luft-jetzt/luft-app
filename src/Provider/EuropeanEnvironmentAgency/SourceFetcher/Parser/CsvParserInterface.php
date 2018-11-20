<?php declare(strict_types=1);

namespace App\Provider\EuropeanEnvironmentAgency\SourceFetcher\Parser;

interface CsvParserInterface
{
    public function parse(string $csvFileContent): array;
}
