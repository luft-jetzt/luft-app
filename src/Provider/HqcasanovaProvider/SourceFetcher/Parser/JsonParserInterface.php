<?php declare(strict_types=1);

namespace App\Provider\HqcasanovaProvider\SourceFetcher\Parser;

interface JsonParserInterface
{
    public function parse(string $jsonData): array;
}
