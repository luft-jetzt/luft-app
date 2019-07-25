<?php declare(strict_types=1);

namespace App\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use App\Pollution\Value\Value;

interface JsonParserInterface
{
    public function parseUVIndex(string $jsonData): Value;
    public function parseTemperature(string $jsonData): Value;
}
