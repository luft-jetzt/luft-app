<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

interface JsonParserInterface
{
    public function parse(string $jsonData): array;
}
