<?php declare(strict_types=1);

namespace App\Provider\Luftdaten\SourceFetcher\Parser;

interface JsonParserInterface
{
    public function parse(array $data): array;
}
