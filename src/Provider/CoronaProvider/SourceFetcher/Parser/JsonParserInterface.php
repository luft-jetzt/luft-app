<?php declare(strict_types=1);

namespace App\Provider\CoronaProvider\SourceFetcher\Parser;

use App\Pollution\Value\Value;

interface JsonParserInterface
{
    public function parseCoronaIncidence(string $jsonData): Value;
}
