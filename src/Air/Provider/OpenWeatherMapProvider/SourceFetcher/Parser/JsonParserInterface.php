<?php declare(strict_types=1);

namespace App\Air\Provider\OpenWeatherMapProvider\SourceFetcher\Parser;

use Caldera\LuftModel\Model\Value;

interface JsonParserInterface
{
    public function parseUVIndex(string $jsonData): Value;
    public function parseTemperature(string $jsonData): Value;
}
