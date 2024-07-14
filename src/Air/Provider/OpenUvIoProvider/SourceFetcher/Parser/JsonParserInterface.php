<?php declare(strict_types=1);

namespace App\Air\Provider\OpenUvIoProvider\SourceFetcher\Parser;

use App\Air\Value\Value;

interface JsonParserInterface
{
    public function parseUVIndex(string $jsonData): Value;
    public function parseUVIndexMax(string $jsonData): Value;
}
